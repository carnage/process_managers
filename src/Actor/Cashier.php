<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\HandleOrderInterface;
use ProcessManagers\Model\Order;

class Cashier implements HandleOrderInterface
{
    /**
     * @var HandleOrderInterface
     */
    private $next;

    public function __construct(HandleOrderInterface $next)
    {
        $this->next = $next;
    }

    public function handle(Order $order)
    {
        $order->paid();

        $this->next->handle($order);
    }
}