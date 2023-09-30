<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\Chat_request;

use App\Models\Chats;

use Illuminate\Http\Request;

use Ratchet\MessageComponentInterface;

use Ratchet\ConnectionInterface;

class SocketController extends Controller implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Handle new WebSocket connections
        $this->clients->attach($conn);
        $data=[
            'user'=>'server',
            'msg'=>'Welcome to the WebSocket server!',
            'img'=>'avatars/empty_pp.jpg',
        ];
        // $conn->send(json_encode($data));
        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);
        if (isset($queryarray['token'])) {
            $token = $queryarray['token'];

            // Update the user table based on the token
            User::where('token', $token)->update([
                'connection_id' => $conn->resourceId,
                'user_status' => 'Online',
            ]);
    }
}


public function onMessage(ConnectionInterface $conn, $msg) {
    // Handle WebSocket messages
    $data = json_decode($msg);

    if (isset($data->type) && $data->type == 'msg') {

        $chat = new Chats;
        $chat->from_user_id=$data->from_user_id;
        $chat->to_user_id=$data->to_user_id;
        $chat->chat_message=$data->chat_message;
        $chat->save();



        $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();
        $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get();
        foreach ($this->clients as $client) {
            if ($client->resourceId == $receiver_connection_id[0]->connection_id) {
                $client->send(json_encode($data));
            }
            if($client->resourceId == $sender_connection_id[0]->connection_id){
                $client->send(json_encode($data));
            }
        }
    }
    if (isset($data->type)) {
        if ($data->type == 'load_all_users') {
            $user_data = User::select('id', 'name', 'user_status', 'user_image')
                ->where('id', '!=', $data->from_user_id)
                ->where('name', 'like', '%' . $data->search_query . '%')
                ->orderBy('name', 'ASC')
                ->get();

            $sub_data = array();

            foreach ($user_data as $row) {
                $chat_request = Chat_request::select('id')
                    ->where(function ($query) use ($data, $row) {
                        $query->where('from_user_id', $data->from_user_id)->where('to_user_id', $row->id);
                    })
                    ->orWhere(function ($query) use ($data, $row) {
                        $query->where('from_user_id', $row->id)->where('to_user_id', $data->from_user_id);
                    })->get();

                if ($chat_request->count() == 0) {
                    $sub_data[] = array(
                        'name' => $row['name'],
                        'id' => $row['id'],
                        'status' => $row['user_status'],
                        'user_image' => $row['user_image']
                    );
                }
            }

            $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();
            $send_data['data'] = $sub_data;
            $send_data['type'] = 'load_all_users';
            $send_data['response_load_unconnected_user'] = true;

            foreach ($this->clients as $client) {
                if ($client->resourceId == $sender_connection_id[0]->connection_id) {
                    $client->send(json_encode($send_data));
                }
            }
        }

        if ($data->type == 'request_chat_user') {
            $chat_request = new Chat_request;
            $chat_request->from_user_id = $data->from_user_id;
            $chat_request->to_user_id = $data->to_user_id;
            $chat_request->status = 'Pending';
            $chat_request->save();

            $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();
            $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get();
            $send_data['type'] = 'updateUI';

            foreach ($this->clients as $client) {
                if ($client->resourceId == $sender_connection_id[0]->connection_id) {
                    $send_data['response_from_user_chat_request'] = true;
                    $client->send(json_encode($send_data));
                }

                if ($client->resourceId == $receiver_connection_id[0]->connection_id) {
                    $send_data['user_id'] = $data->to_user_id;
                    $send_data['response_to_user_chat_request'] = true;
                    $client->send(json_encode($send_data));
                }
            }
        }

        if ($data->type == 'load_notifications') {
            $notification_data = Chat_request::select('id', 'from_user_id', 'to_user_id', 'status')
                ->where('status', '!=', 'Approve')
                ->where(function ($query) use ($data) {
                    $query->where('from_user_id', $data->user_id)->orWhere('to_user_id', $data->user_id);
                })
                ->orderBy('id', 'ASC')
                ->get();

            $sub_data = array();

            foreach ($notification_data as $row) {
                $user_id = '';
                $notification_type = '';

                if ($row->from_user_id == $data->user_id) {
                    $user_id = $row->to_user_id;
                    $notification_type = 'Send Request';
                } else {
                    $user_id = $row->from_user_id;
                    $notification_type = 'Receive Request';
                }

                $user_data = User::select('name', 'user_image')->where('id', $user_id)->first();

                $sub_data[] = array(
                    'id' => $row->id,
                    'from_user_id' => $row->from_user_id,
                    'to_user_id' => $row->to_user_id,
                    'name' => $user_data->name,
                    'notification_type' => $notification_type,
                    'status' => $row->status,
                    'user_image' => $user_data->user_image
                );
            }

            $sender_connection_id = User::select('connection_id')->where('id', $data->user_id)->get();

            foreach ($this->clients as $client) {
                if ($client->resourceId == $sender_connection_id[0]->connection_id) {
                    $send_data['type'] = 'response_load_notification';
                    $send_data['data'] = $sub_data;
                    $client->send(json_encode($send_data));
                }
            }
        }

        if ($data->type == 'request_connected_chat_user') {
            $condition_1 = ['from_user_id' => $data->from_user_id, 'to_user_id' => $data->from_user_id];
            $user_id_data = Chat_request::select('from_user_id', 'to_user_id')
                ->orWhere($condition_1)
                ->where('status', 'Approve')
                ->get();

            $sub_data = array();

            foreach ($user_id_data as $user_id_row) {
                $user_id = '';

                if ($user_id_row->from_user_id != $data->from_user_id) {
                    $user_id = $user_id_row->from_user_id;
                } else {
                    $user_id = $user_id_row->to_user_id;
                }

                $user_data = User::select('id', 'name', 'user_image', 'user_status', 'updated_at')->where('id', $user_id)->first();

                if (date('Y-m-d') == date('Y-m-d', strtotime($user_data->updated_at))) {
                    $last_seen = 'Last Seen At ' . date('H:i', strtotime($user_data->updated_at));
                } else {
                    $last_seen = 'Last Seen At ' . date('d/m/Y H:i', strtotime($user_data->updated_at));
                }

                $sub_data[] = array(
                    'id' => $user_data->id,
                    'name' => $user_data->name,
                    'user_image' => $user_data->user_image,
                    'user_status' => $user_data->user_status,
                    'last_seen' => $last_seen
                );
            }

            $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

            foreach ($this->clients as $client) {
                if ($client->resourceId == $sender_connection_id[0]->connection_id) {
                    $send_data['type'] = 'load_connected_users';
                    $send_data['data'] = $sub_data;
                    $client->send(json_encode($send_data));
                }
            }
        }

        if ($data->type == 'request_process_chat_request') {
            Chat_request::where('id', $data->chat_request_id)->update(['status' => $data->action]);
            $sender_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();
            $receiver_connection_id = User::select('connection_id')->where('id', $data->to_user_id)->get();

            foreach ($this->clients as $client) {
                $send_data['type'] = 'response_process_chat_request';

                if ($client->resourceId == $sender_connection_id[0]->connection_id) {
                    $send_data['user_id'] = $data->from_user_id;
                }

                if ($client->resourceId == $receiver_connection_id[0]->connection_id) {
                    $send_data['user_id'] = $data->to_user_id;
                }

                $client->send(json_encode($send_data));
            }
        }
        if($data->type=='loadOldMsg'){
            $chat_data = Chats::select('chats.id', 'chats.from_user_id', 'chats.to_user_id', 'chats.chat_message','chats.created_at', 'users.name','users.user_image')
            ->join('users', 'users.id', '=', 'chats.from_user_id')
            ->where(function ($query) use ($data) {
                $query->where('chats.from_user_id', $data->from_user_id)
                    ->where('chats.to_user_id', $data->to_user_id);
            })
            ->orWhere(function ($query) use ($data) {
                $query->where('chats.from_user_id', $data->to_user_id)
                    ->where('chats.to_user_id', $data->from_user_id);
            })
            ->orderBy('chats.id', 'ASC')
            ->get();




                                    $send_data['data'] = $chat_data;
                                    $send_data['type'] = 'chat_history';
                                    $receiver_connection_id = User::select('connection_id')->where('id', $data->from_user_id)->get();

                                    foreach($this->clients as $client)
                                    {
                                        if($client->resourceId == $receiver_connection_id[0]->connection_id)
                                        {

                                            $client->send(json_encode($send_data));
                                        }
                                    }
        }
    }
}


    public function onClose(ConnectionInterface $conn) {
        // Handle WebSocket connection close
        $this->clients->detach($conn);
        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);
        if (isset($queryarray['token'])) {
            $token = $queryarray['token'];

            // Update the user table based on the token
            User::where('token', $token)->update([
                'connection_id' => 0,
                'user_status' => 'Offline',
            ]);
    }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Handle WebSocket errors
        echo "An error has occurred: {$e->getMessage()} \n";

        $conn->close();
    }
}
