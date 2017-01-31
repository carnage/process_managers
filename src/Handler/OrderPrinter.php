<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;

class OrderPrinter implements HandleOrderInterface
{
    public function handle(Order $order)
    {
        //var_dump($order);
        echo $order->getTableNumber() . ': ' . $order->getOrderId() . "\n";
    }
}
