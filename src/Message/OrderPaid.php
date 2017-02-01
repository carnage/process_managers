<?php

namespace ProcessManagers\Message;

use ProcessManagers\Model\Order;

class OrderPaid extends AbstractMessage
{
    private $order;

    public function __construct(string $id, string $causeId, string $corrId, Order $order)
    {
        parent::__construct($id, $causeId, $corrId);
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