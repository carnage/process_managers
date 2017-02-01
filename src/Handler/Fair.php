<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Model\Order;
use React\EventLoop\LoopInterface;

class Fair implements HandleMessageInterface
{
    use AlwaysReady;
    private $handlers;
    /**
     * @var LoopInterface
     */
    private $loop;

    private $queue = [];

    public function __construct(LoopInterface $loop, HandleMessageInterface ... $handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
        $this->loop = $loop;
        $loop->addPeriodicTimer(0.1, function () {
            $handler = array_shift($this->handlers);
            if ($handler instanceof QueueLengthInterface && $handler->getQueueLength() < 2) {
                $order = array_shift($this->queue);
                if ($order) {
                    $handler->handle($order);
                }
            }
            array_push($this->handlers, $handler);
        });
    }

    public function handle(MessageInterface $order)
    {
        array_push($this->queue, $order);
    }

    public static function getHandleMethod(string $message): string
    {
        return 'handle';
    }
}