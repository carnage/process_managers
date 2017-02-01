<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;
use React\EventLoop\LoopInterface;

class TtlQueue implements HandleMessageInterface, QueueLengthInterface
{
    private $ttl = 5000;
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
                $order = $this->nextQueueItem();
                if ($order) {
                    $this->handler->handle($order);
                }
            }
        });
        $this->name = $name;
    }

    public function handle(MessageInterface $order)
    {
        array_push($this->queue, ['t'=> microtime(true), 'o' => $order]);
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
    /**
     * @param $this
     * @return mixed
     */
    private function nextQueueItem()
    {
        if (count($this->queue)) {
            return null;
        }

        $order = array_shift($this->queue);
        if (($order['t'] + $this->ttl) < microtime(true)) {
            return $order['o'];
        }

        return $this->nextQueueItem();
    }
}