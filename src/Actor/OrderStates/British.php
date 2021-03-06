<?php

namespace ProcessManagers\Actor\OrderStates;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\AlwaysReady;
use ProcessManagers\Message\CookFood;
use ProcessManagers\Message\CookingTimedOut;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderCookedTwice;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\Message\OrderSpiked;
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

    private $cooking = false;

    private $cooked = false;

    public function __construct(MessageFactory $messageFactory, callable $done)
    {
        $this->done = $done;
        $this->queue = new class implements PublishInterface {
            public function publish(MessageInterface $message)
            {
            }
        };

        $this->messageFactory = $messageFactory;
    }

    public function goLive(PublishInterface $queue)
    {
        $this->queue = $queue;
    }

    public function handleOrderPlaced(OrderPlaced $orderMessage)
    {
        $this->doCook($orderMessage);
    }

    public function handleOrderCooked(OrderCooked $orderMessage)
    {
        $this->cooking = false;
        if ($this->cooked) {
            $this->queue->publish(
                $this->messageFactory->createOrderMessageFromPrevious(OrderCookedTwice::class, $orderMessage)
            );
        } else {
            $this->cooked = true;
            $this->queue->publish(
                $this->messageFactory->createOrderMessageFromPrevious(PriceOrder::class, $orderMessage)
            );
        }
    }

    public function handleCookingTimedOut(CookingTimedOut $orderMessage)
    {
        if ($this->cooking) {
            $this->doCook($orderMessage);
        }
    }

    public function handleOrderPriced(OrderPriced $orderMessage)
    {
        $this->queue->publish(
            $this->messageFactory->createOrderMessageFromPrevious(TakePayment::class, $orderMessage)
        );
    }

    public function handleOrderPaid(OrderPaid $orderMessage)
    {
        $this->queue->publish(
            $this->messageFactory->createOrderMessageFromPrevious(OrderSpiked::class, $orderMessage)
        );
        $done = $this->done;
        $done();
    }

    /**
     * @param MessageInterface $orderMessage
     */
    private function doCook(MessageInterface $orderMessage)
    {
        $this->queue->publish(
            $this->messageFactory->createOrderMessageFromPrevious(CookFood::class, $orderMessage)
        );

        $this->cooking = true;
        $this->queue->publish(
            $this->messageFactory->createDelayedMessage(
                30,
                $this->messageFactory->createOrderMessageFromPrevious(CookingTimedOut::class, $orderMessage)
            )
        );
    }
}