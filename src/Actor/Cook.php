<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\MessageQueue;
use ProcessManagers\Model\Order;
use ProcessManagers\PublishInterface;
use React\EventLoop\LoopInterface;

class Cook extends AbstractMessageHandler
{
    /**
     * @var
     */
    private $cookTime;

    /**
     * @var PublishInterface
     */
    private $queue;
    /**
     * @var
     */
    private $name;
    /**
     * @var LoopInterface
     */
    private $loop;
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(LoopInterface $loop, PublishInterface $queue, MessageFactory $messageFactory, $cookTime, $name)
    {
        $this->cookTime = $cookTime;
        $this->queue = $queue;
        $this->name = $name;
        $this->loop = $loop;
        $this->messageFactory = $messageFactory;
    }

    protected function handleOrderPlaced(OrderPlaced $orderPlaced)
    {
        $order = $orderPlaced->getOrder();
        $ingredients = $this->getIngredientsFor($order->getItems());
        $order->cook($this->cookTime, $ingredients, $this->name);

        $this->loop->addTimer($this->cookTime, function () use ($orderPlaced) {
            $this->queue->publish(
                $this->messageFactory->createOrderMessageFromPrevious(OrderCooked::class, $orderPlaced)
            );
            $this->ready = true;
        });
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