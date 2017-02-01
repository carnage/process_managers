<?php

namespace ProcessManagers\Actor\OrderStates;

use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Model\Order;
use ProcessManagers\PublishInterface;

class OrderStateFactory
{
    private $queue;
    private $messageFactory;

    /**
     * OrderStateFactory constructor.
     * @param $queue
     * @param $messageFactory
     */
    public function __construct(PublishInterface $queue, MessageFactory $messageFactory)
    {
        $this->queue = $queue;
        $this->messageFactory = $messageFactory;
    }


    public function createProcess(Order $order, callable $done)
    {
        if ($order->isDodgy()) {
            return new Zimba(
                $this->queue,
                $this->messageFactory,
                $done
            );
        } else {
            return new British(
                $this->queue,
                $this->messageFactory,
                $done
            );
        }
    }
}