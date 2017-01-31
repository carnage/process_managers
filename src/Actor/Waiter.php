<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\HandleOrderInterface;
use ProcessManagers\Model\Order;
use ProcessManagers\UUID;

class Waiter
{
    private $next;
    /**
     * @var UUID
     */
    private $UUID;

    /**
     * Waiter constructor.
     * @param $next
     */
    public function __construct(HandleOrderInterface $next, UUID $UUID)
    {
        $this->next = $next;
        $this->UUID = $UUID;
    }

    public function placeOrder(int $tableNumber, array $items): string
    {
        $uuid = $this->UUID->generateIdentity();
        $order = new Order($uuid, $tableNumber, $items);

        $this->next->handle($order);

        return $uuid;
    }
}