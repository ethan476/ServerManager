<?php

use \Ratchet\MessageComponentInterface;
use \Guzzle\Http\Message\RequestInterface;
use \Ratchet\ConnectionInterface;

class WsMessageRouter implements MessageComponentInterface
{
    public function __construct()
    {

    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        $conn->send("Time: " . time());
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        //$packet = json_decode($msg, true);
    }

    public function onClose(ConnectionInterface $conn)
    {

    }
}