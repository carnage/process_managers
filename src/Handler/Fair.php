<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;

class Fair implements HandleOrderInterface
{
    private $handlers;

    public function __construct(HandleOrderInterface ... $handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
    }

    public function handle(Order $order)
    {
        $handler = array_shift($this->handlers);
        if ($handler instanceof QueueLengthInterface && $handler->getQueueLength() < 5) {
            $handler->handle($order);
        }
        array_push($this->handlers, $handler);
    }
}