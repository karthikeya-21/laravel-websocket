<?php

namespace App\Http\Controllers;

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
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Handle WebSocket errors
        echo "An error has occurred: {$e->getMessage()} \n";

        $conn->close();
    }
}
