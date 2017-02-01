<?php

namespace ProcessManagers\Message;

use ProcessManagers\Model\Order;
use ProcessManagers\UUID;

class MessageFactory
{
    private $uuid;

    /**
     * MessageFactory constructor.
     * @param $uuid
     */
    public function __construct(UUID $uuid)
    {
        $this->uuid = $uuid;
    }

    public function createOrderMessage(string $type, Order $order)
    {
        $messageId = $this->uuid->generateIdentity();
        $corrId = $order->getOrderId();
        $causeId = '';

        return new $type($messageId, $causeId, $corrId, $order);
    }

    public function createOrderMessageFromPrevious(string $type, AbstractMessage $previous)
    {
        $messageId = $this->uuid->generateIdentity();
        $corrId = $previous->getCorrId();
        $causeId = $previous->getId();

        return new $type($messageId, $causeId, $corrId, $previous->getOrder());
    }
}