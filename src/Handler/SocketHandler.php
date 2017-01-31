<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;
use React\EventLoop\LoopInterface;

class SocketHandler implements HandleOrderInterface, QueueLengthInterface
{
    private $connDetails;
    /**
     * @var LoopInterface
     */
    private $loop;

    private $waiting = 0;

    public function __construct($connDetails, LoopInterface $loop)
    {
        $this->connDetails = $connDetails;
        $this->loop = $loop;
        $this->loop->addPeriodicTimer(1, function () {
            echo $this->waiting . "\n";
        });
    }

    public function handle(Order $order)
    {
        $message = serialize($order);
        $errno = $errstr = '';

        $fp = stream_socket_client($this->connDetails, $errno, $errstr, 30);
        stream_set_blocking($fp, false);
        fwrite($fp, $message);
        fwrite($fp, "\r\n\r\n");

        $this->waiting++;

        $this->loop->addPeriodicTimer(1, function() use ($fp) {
            $meta = stream_get_meta_data($fp);
            var_dump($meta);
            if ($meta['eof'] === true) {
                $this->waiting--;
            }
        });
    }

    /**
     * @return int
     */
    public function getQueueLength(): int
    {
        return $this->waiting;
    }
}