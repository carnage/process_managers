<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\HandleOrderInterface;
use ProcessManagers\Model\Order;

class AssistantManager implements HandleOrderInterface
{
    /**
     * @var HandleOrderInterface
     */
    private $next;

    const TAX_RATE = 20;

    public function __construct(HandleOrderInterface $next)
    {
        $this->next = $next;
    }

    public function handle(Order $order)
    {
        $subTotal = $this->calculatePrice($order->getIngredients(), $order->getCookTime());

        $tax = $subTotal * (self::TAX_RATE / 100);
        $order->addPrices($subTotal, $tax, $subTotal+$tax);

        $this->next->handle($order);
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