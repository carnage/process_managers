<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;

class RoundRobin implements HandleOrderInterface
{
    private $handlers;

    public function __construct(HandleOrderInterface ... $handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
    }

    public function handle(Order $order)
    {
        $handler = array_shift($this->handlers);
        $handler->handle($order);
        array_push($this->handlers, $handler);
    }
}