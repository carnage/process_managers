<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Actor\OrderStates\British;
use ProcessManagers\Actor\OrderStates\OrderStateFactory;
use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\AlwaysReady;
use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Handler\QueueLengthInterface;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\PublishInterface;

class Runner extends AbstractMessageHandler implements QueueLengthInterface
{
    use AlwaysReady;
    private $queue;

    private $orders;
    /**
     * @var OrderStateFactory
     */
    private $orderStateFactory;
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, PublishInterface $queue, OrderStateFactory $orderStateFactory)
    {
        $this->queue = $queue;
        $this->orderStateFactory = $orderStateFactory;
        $this->name = $name;
    }

    public function handleOrderPlaced(OrderPlaced $orderMessage)
    {
        $this->orders[$orderMessage->getCorrId()] = 'I care about this';
        $this->handleMessage($orderMessage);
    }

    public function handleMessage(MessageInterface $orderMessage)
    {
        if (!method_exists($orderMessage, 'getOrder')) {
            return;
        }

        $corrId = $orderMessage->getCorrId();
        $done = function () use ($corrId) {
            unset($this->orders[$corrId]);
        };
        $pm = $this->orderStateFactory->createProcess($orderMessage->getOrder(), $done);
        foreach ($this->queue->getHistory($corrId) as $message) {
            $pm->handle($message);
        }

        $pm->goLive($this->queue);
        $pm->handle($orderMessage);
    }

    public static function getHandleMethod(string $message): string
    {
        if ($message === OrderPlaced::class) {
            return 'handleOrderPlaced';
        }

        return 'handleMessage';
    }

    public function getQueueLength(): int
    {
        return count($this->orders);
    }

    public function getName(): string
    {
        return $this->name;
    }
}