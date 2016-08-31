<?php

use \Guzzle\Http\Message\RequestInterface;
use \Ratchet\ConnectionInterface;
use \Ratchet\Http\HttpServerInterface;
use \Guzzle\Http\Message\Response;

class HttpRouter implements HttpServerInterface
{
    protected $m_response;

    public function __construct()
    {

    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        $this->m_response = new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ], $request->getUrl(), '1.1');

        $this->close($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e )
    {

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

    }

    public function onClose(ConnectionInterface $conn)
    {

    }

    protected function close(ConnectionInterface $conn)
    {
        $conn->send((string) $this->m_response);
        $conn->close();
    }
}