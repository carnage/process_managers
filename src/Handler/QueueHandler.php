<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;
use React\EventLoop\LoopInterface;

class QueueHandler implements HandleMessageInterface, QueueLengthInterface
{
    use AlwaysReady;
    private $handler;
    private $queue = [];
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, HandleMessageInterface $handler, LoopInterface $loop)
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
        $this->name = $name;
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}