<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;

class Repeater implements HandleMessageInterface
{
    private $handlers;

    public function __construct(HandleMessageInterface ...$handleOrderInterfaces)
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