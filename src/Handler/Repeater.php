<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;

class Repeater implements HandleOrderInterface
{
    private $handlers;

    public function __construct(HandleOrderInterface ...$handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
    }

    public function handle(Order $order)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($order);
        }
    }
}