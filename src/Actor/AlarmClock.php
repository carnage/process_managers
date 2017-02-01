<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\Message\PriceOrder;
use ProcessManagers\Message\PublishAt;
use ProcessManagers\Message\TakePayment;
use ProcessManagers\PublishInterface;
use React\EventLoop\LoopInterface;

class AlarmClock extends AbstractMessageHandler
{
    /**
     * @var PublishInterface
     */
    private $queue;

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop, PublishInterface $queue)
    {
        $this->queue = $queue;
        $this->loop = $loop;
    }

    protected function handlePublishAt(PublishAt $message)
    {
        $this->loop->addTimer($message->getTtl(),function () use ($message) {
            $this->queue->publish(
                $message->getMessage()
            );
        });
        $this->ready = true;
    }
}