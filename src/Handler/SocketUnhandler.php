<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;
use React\Socket\ConnectionInterface;
use React\Socket\Server;

class SocketUnhandler
{
    /**
     * @var HandleOrderInterface
     */
    private $handler;

    public function __construct(Server $socket, HandleOrderInterface $handler)
    {
        $socket->on('connection', [$this, 'onConnect']);

        $this->handler = $handler;
    }

    public function onConnect(ConnectionInterface $conn)
    {
        $buffer = '';
        $conn->on('data', function ($data) use (&$buffer, $conn) {
            $buffer .= $data;

            if (substr($buffer, -4) === "\r\n\r\n") {
                /** @var Order $message */
                $message = unserialize($buffer);
                /** @var HandleOrderInterface */
                $this->handler->handle($message);

                $conn->close();
            }
        });
    }
}