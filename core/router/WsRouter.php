<?php

use \Ratchet\WebSocket\WsServer;
use \Ratchet\ConnectionInterface;
use \Guzzle\Http\Message\Response;

class WsRouter extends WsServer
{
    protected function close(ConnectionInterface $conn, $code = 400)
    {
        $response = new Response($code, array(
            'Sec-WebSocket-Version' => $this->versioner->getSupportedVersionString(),
            'X-Powered-By'          => \Ratchet\VERSION,
        ));

        $conn->send((string) $response);
        $conn->close();
    }

    protected function redirect(ConnectionInterface $conn, $url = "http://localhost:8080")
    {
        $response = new Response(302, array(
            'Sec-WebSocket-Version' => $this->versioner->getSupportedVersionString(),
            'X-Powered-By'          => \Ratchet\VERSION,
            'Location'              => $url
        ));

        $conn->send((string) $response);
        $conn->close();
    }

    protected function attemptUpgrade(ConnectionInterface $conn, $data = '')
    {
        if ('' !== $data)
        {
            $conn->WebSocket->request->getBody()->write($data);
        }

        if (!$this->versioner->isVersionEnabled($conn->WebSocket->request))
        {
            //return $this->close($conn);
            return $this->redirect($conn);
        }

        $conn->WebSocket->version = $this->versioner->getVersion($conn->WebSocket->request);
        try
        {
            $response = $conn->WebSocket->version->handshake($conn->WebSocket->request);
        }
        catch (\UnderflowException $e)
        {
            return;
        }

        if (null !== ($subHeader = $conn->WebSocket->request->getHeader('Sec-WebSocket-Protocol')))
        {
            if ('' !== ($agreedSubProtocols = $this->getSubProtocolString($subHeader->normalize())))
            {
                $response->setHeader('Sec-WebSocket-Protocol', $agreedSubProtocols);
            }
        }

        $response->setHeader('X-Powered-By', \Ratchet\VERSION);
        $conn->send((string) $response);

        if (101 != $response->getStatusCode())
        {
            return $this->redirect($conn);
        }

        $upgraded = $conn->WebSocket->version->upgradeConnection($conn, $this->component);
        $this->connections->attach($conn, $upgraded);
        $upgraded->WebSocket->established = true;
        return $this->component->onOpen($upgraded);
    }
}