<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\MessageQueue;
use ProcessManagers\Model\Order;
use ProcessManagers\PublishInterface;

class Cook extends AbstractMessageHandler
{
    /**
     * @var
     */
    private $cookTime;

    /**
     * @var PublishInterface
     */
    private $queue;
    /**
     * @var
     */
    private $name;

    public function __construct(PublishInterface $queue, $cookTime, $name)
    {
        $this->cookTime = $cookTime;
        $this->queue = $queue;
        $this->name = $name;
    }

    protected function handleOrderPlaced(OrderPlaced $orderPlaced)
    {
        $order = $orderPlaced->getOrder();
        $ingredients = $this->getIngredientsFor($order->getItems());
        $order->cook($this->cookTime, $ingredients, $this->name);
        $this->queue->publish(new OrderCooked($order));
    }

    private function getIngredientsFor($items)
    {
        $ingredients = [];
        foreach ($items as $item) {
            $ingredients[] = 'screws';
        }

        return $ingredients;
    }
}