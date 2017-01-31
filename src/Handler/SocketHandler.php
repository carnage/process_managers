<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;
use React\EventLoop\LoopInterface;

class SocketHandler implements HandleMessageInterface
{
    private $connDetails;
    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct($connDetails, LoopInterface $loop)
    {
        $this->connDetails = $connDetails;
        $this->loop = $loop;
    }

    public function handle(Order $order)
    {
        $message = serialize($order);
        $errno = $errstr = '';

        $fp = stream_socket_client($this->connDetails, $errno, $errstr, 30);
        stream_set_blocking($fp, false);
        fwrite($fp, $message);
        fwrite($fp, "\r\n\r\n");
    }
}