<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\PublishInterface;

class Cashier extends AbstractMessageHandler
{
    /**
     * @var PublishInterface
     */
    private $queue;

    public function __construct(PublishInterface $queue)
    {
        $this->queue = $queue;
    }

    protected function handleOrderPriced(OrderPriced $orderPriced)
    {
        $order = $orderPriced->getOrder();
        $order->paid();

        $this->queue->publish(new OrderPaid($order));
    }
}