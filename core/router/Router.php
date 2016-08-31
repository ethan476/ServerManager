<?php

use Guzzle\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;

class Router implements HttpServerInterface
{
    protected $m_routes = array();

    protected $m_controller;

    protected $m_uri;

    public function __construct()
    {

    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        if (null === $request)
        {
            throw new \UnexpectedValueException('$request can not be null');
        }

        // Websocket or HTTP Request
        if (trim(parse_url($request->getUrl(), PHP_URL_PATH), '/') == 'ws')
        {
            $this->m_controller = new WsRouter(new WsMessageRouter());
        }
        else
        {
            $this->m_controller = new HttpRouter();
        }

        if (!($this->m_controller instanceof HttpServerInterface))
        {
            throw new \UnexpectedValueException('All routes must implement \Ratchet\Http\HttpServerInterface');
        }

        $this->m_controller->onOpen($conn, $request);
    }

    public function onError(ConnectionInterface $conn, \Exception $e )
    {
        $this->m_controller->onError($conn, $e);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->m_controller->onMessage($from, $msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->m_controller->onClose($conn);
    }

    protected function close(ConnectionInterface $conn)
    {
        if (method_exists($this->m_controller, 'close'))
        {
            $this->m_controller->close($conn);
        }
        else
        {
            $conn->close();
        }
    }
}