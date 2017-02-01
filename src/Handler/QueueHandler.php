<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;
use React\EventLoop\LoopInterface;

class QueueHandler implements HandleMessageInterface, QueueLengthInterface
{
    use AlwaysReady;
    private $handler;
    private $queue = [];

    public function __construct(HandleMessageInterface $handler, LoopInterface $loop)
    {
        $this->handler = $handler;
        $loop->addPeriodicTimer(0.1, function () {
            if ($this->handler->isReady()) {
                $order = array_shift($this->queue);
                if ($order) {
                    $this->handler->handle($order);
                }
            }
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

    public function getQueueLength(): int
    {
        return count($this->queue);
    }
}