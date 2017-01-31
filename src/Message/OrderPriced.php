<?php

namespace ProcessManagers\Message;

use ProcessManagers\Model\Order;

class OrderPriced implements MessageInterface
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }
}