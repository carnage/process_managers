<?php

namespace ProcessManagers\Actor\OrderStates;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\AlwaysReady;
use ProcessManagers\Message\CookFood;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\Message\PriceOrder;
use ProcessManagers\Message\TakePayment;
use ProcessManagers\PublishInterface;

class British extends AbstractMessageHandler
{
    use AlwaysReady;

    /**
     * @var callable
     */
    private $done;
    /**
     * @var PublishInterface
     */
    private $queue;
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(PublishInterface $queue, MessageFactory $messageFactory, callable $done)
    {
        $this->done = $done;
        $this->queue = $queue;
        $this->messageFactory = $messageFactory;
    }

    public function handleOrderPlaced(OrderPlaced $orderMessage)
    {
        $this->queue->publish(
            $this->messageFactory->createOrderMessageFromPrevious(CookFood::class, $orderMessage)
        );
    }

    public function handleOrderCooked(OrderCooked $orderMessage)
    {
        $this->queue->publish(
            $this->messageFactory->createOrderMessageFromPrevious(PriceOrder::class, $orderMessage)
        );
    }

    public function handleOrderPriced(OrderPriced $orderMessage)
    {
        $this->queue->publish(
            $this->messageFactory->createOrderMessageFromPrevious(TakePayment::class, $orderMessage)
        );
    }

    public function handleOrderPaid(OrderPaid $orderMessage)
    {
        $done = $this->done;
        $done();
    }
}