<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Model\Order;
use React\EventLoop\LoopInterface;

class Fair implements HandleMessageInterface, QueueLengthInterface
{
    use AlwaysReady;
    private $handlers;
    /**
     * @var LoopInterface
     */
    private $loop;

    private $queue = [];
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, LoopInterface $loop, HandleMessageInterface ... $handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
        $this->loop = $loop;
        $loop->addPeriodicTimer(0.1, function () {
            $handler = array_shift($this->handlers);
            if ($handler instanceof QueueLengthInterface && $handler->getQueueLength() < 1) {
                $order = array_shift($this->queue);
                if ($order) {
                    $handler->handle($order);
                }
            }
            array_push($this->handlers, $handler);
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