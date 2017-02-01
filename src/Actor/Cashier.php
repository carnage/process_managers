<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\Message\PriceOrder;
use ProcessManagers\Message\TakePayment;
use ProcessManagers\PublishInterface;
use React\EventLoop\LoopInterface;

class Cashier extends AbstractMessageHandler
{
    /**
     * @var PublishInterface
     */
    private $queue;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(LoopInterface $loop, PublishInterface $queue, MessageFactory $messageFactory)
    {
        $this->queue = $queue;
        $this->loop = $loop;
        $this->messageFactory = $messageFactory;
    }

    protected function handleTakePayment(TakePayment $orderPriced)
    {
        $order = $orderPriced->getOrder();
        $order->paid();

        $this->loop->addTimer(1,function () use ($orderPriced) {
            $this->queue->publish(
                $this->messageFactory->createOrderMessageFromPrevious(OrderPaid::class, $orderPriced)
            );
            $this->ready = true;
        });
    }
}