<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Actor\OrderStates\British;
use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\AlwaysReady;
use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\PublishInterface;

class Runner extends AbstractMessageHandler
{
    use AlwaysReady;
    private $queue;
    /** @var  HandleMessageInterface[] */
    private $orders;
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(PublishInterface $queue, MessageFactory $messageFactory)
    {
        $this->queue = $queue;
        $this->messageFactory = $messageFactory;
    }

    public function handleOrderPlaced(OrderPlaced $orderMessage)
    {
        $corrId = $orderMessage->getCorrId();
        $done = function () use ($corrId) {
            unset($this->orders[$corrId]);
        };
        $this->orders[$corrId] = new British(
            $this->queue,
            $this->messageFactory,
            $done
        );

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
}