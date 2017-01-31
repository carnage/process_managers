<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\PublishInterface;

class AssistantManager extends AbstractMessageHandler
{
    /**
     * @var PublishInterface
     */
    private $queue;

    const TAX_RATE = 20;

    public function __construct(PublishInterface $queue)
    {
        $this->queue = $queue;
    }

    protected function handleOrderCooked(OrderCooked $orderCooked)
    {
        $order = $orderCooked->getOrder();
        $subTotal = $this->calculatePrice($order->getIngredients(), $order->getCookTime());

        $tax = $subTotal * (self::TAX_RATE / 100);
        $order->addPrices($subTotal, $tax, $subTotal+$tax);

        $this->queue->publish(new OrderPriced($order));
    }

    private function calculatePrice(array $ingredients, int $cookTime)
    {
        $price = 0;
        foreach ($ingredients as $ingredient) {
            $price++;
        }

        $price += $cookTime * 4;

        return $price;
    }
}