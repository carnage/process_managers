<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\Model\Order;
use ProcessManagers\PublishInterface;
use ProcessManagers\Topics;
use ProcessManagers\UUID;

class Waiter
{
    private $messageQueue;
    /**
     * @var UUID
     */
    private $UUID;

    /**
     * Waiter constructor.
     * @param $messageQueue
     */
    public function __construct(PublishInterface $messageQueue, UUID $UUID)
    {
        $this->messageQueue = $messageQueue;
        $this->UUID = $UUID;
    }

    public function placeOrder(int $tableNumber, array $items): string
    {
        $uuid = $this->UUID->generateIdentity();
        $order = new Order($uuid, $tableNumber, $items);

        $this->messageQueue->publish(new OrderPlaced($order));

        return $uuid;
    }
}