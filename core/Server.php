<?php

use \React\EventLoop\LoopInterface;
use \React\Socket\Server as Reactor;
use \React\EventLoop\Factory as LoopFactory;
use \Ratchet\Server\IoServer;
use \Ratchet\Http\HttpServer;
use Ratchet\Server\FlashPolicy;

class Server
{
    private $m_http_host;

    private $m_port;

    private $m_server;

    public function __construct($http_host = 'localhost', $port = 8080, $address = '127.0.0.1', LoopInterface $loop = null)
    {
        // Encoding check
        if (3 !== strlen('✓'))
        {
            throw new \DomainException('Bad encoding, length of unicode character ✓ should be 3. Ensure charset UTF-8 and check ini val mbstring.func_autoload');
        }

        if (null === $loop)
        {
            $loop = LoopFactory::create();
        }

        $this->m_http_host = $http_host;
        $this->m_port = $port;

        $socket = new Reactor($loop);
        $socket->listen($port, $address);

        $this->m_server = new IoServer(new HttpServer(new Router()), $socket, $loop);

        // Flash stuff
        $policy = new FlashPolicy();
        $policy->addAllowedAccess($http_host, 80);
        $policy->addAllowedAccess($http_host, $port);
        $flashSock = new Reactor($loop);

        $this->flashServer = new IoServer($policy, $flashSock);

        if (80 == $port) {
            $flashSock->listen(843, '0.0.0.0');
        } else {
            $flashSock->listen(8843);
        }

    }

    public function run()
    {
        $this->m_server->run();
    }
}