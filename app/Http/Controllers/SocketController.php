<?php

namespace App\Http\Controllers;

use App\Models\User; 
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
            'msg'=>'Welcome to the WebSocket server!'
        ];
        $conn->send(json_encode($data));
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


    public function onMessage(ConnectionInterface $from, $msg) {
        // Handle WebSocket messages
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                // Send the message to all clients except the sender
                $client->send($msg);
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
