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
    /** @var  HandleMessageInterface[] */
    private $orders;
    /**
     * @var MessageFactory
     */
    private $messageFactory;
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
        $corrId = $orderMessage->getCorrId();
        $done = function () use ($corrId) {
            unset($this->orders[$corrId]);
        };
        $this->orders[$corrId] = $this->orderStateFactory->createProcess($orderMessage->getOrder(), $done);

        $this->handleMessage($orderMessage);
    }

    public function handleMessage(MessageInterface $orderMessage)
    {
        if (isset($this->orders[$orderMessage->getCorrId()])) {
            $this->orders[$orderMessage->getCorrId()]->handle($orderMessage);
        }
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