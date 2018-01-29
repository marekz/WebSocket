<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * 
 * Description of Chat
 *
 */
class Chat implements MessageComponentInterface {
    
    protected $client;
    
    public function __construct(){
        $this->client = new \SplObjectStorage();
    }    

    public function onOpen(ConnectionInterface $conn) {
        $this->client->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->client - 1);
        echo sprintf('Connection %d sending messege "%s" to %d other connections%s' . "\n", 
                $from->resourceId, $msg, $numRecv, $numRecv = 1 ? '' : 's');
        
        foreach ($this->client as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->client->detach($conn);
        
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occured: {$e->getMessage()}\n";
        $conn->close();
    }
}
