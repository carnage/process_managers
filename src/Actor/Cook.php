<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\HandleOrderInterface;
use ProcessManagers\Model\Order;

class Cook implements HandleOrderInterface
{
    /**
     * @var HandleOrderInterface
     */
    private $next;

    const COOK_TIME = 2;

    /**
     * @var
     */
    private $cookTime;

    public function __construct(HandleOrderInterface $next, $cookTime)
    {
        $this->next = $next;
        $this->cookTime = $cookTime;
    }

    public function handle(Order $order)
    {
        $ingredients = $this->getIngredientsFor($order->getItems());
        $order->cook(self::COOK_TIME, $ingredients);
        sleep(self::COOK_TIME);
        $this->next->handle($order);
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