<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\MessageFactory;
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
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * Waiter constructor.
     * @param $messageQueue
     */
    public function __construct(PublishInterface $messageQueue, MessageFactory $messageFactory, UUID $UUID)
    {
        $this->messageQueue = $messageQueue;
        $this->UUID = $UUID;
        $this->messageFactory = $messageFactory;
    }

    public function placeOrder(int $tableNumber, array $items): string
    {
        $uuid = $this->UUID->generateIdentity();
        $order = new Order($uuid, $tableNumber, $items, 0);

        $this->messageQueue->publish(
            $this->messageFactory->createOrderMessage(OrderPlaced::class, $order)
        );

        return $uuid;
    }
}