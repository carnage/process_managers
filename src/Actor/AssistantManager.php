<?php

namespace ProcessManagers\Actor;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Message\MessageFactory;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\PublishInterface;
use React\EventLoop\LoopInterface;

class AssistantManager extends AbstractMessageHandler
{
    /**
     * @var PublishInterface
     */
    private $queue;

    const TAX_RATE = 20;
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

    protected function handleOrderCooked(OrderCooked $orderCooked)
    {
        $order = $orderCooked->getOrder();
        $subTotal = $this->calculatePrice($order->getIngredients(), $order->getCookTime());

        $tax = $subTotal * (self::TAX_RATE / 100);
        $order->addPrices($subTotal, $tax, $subTotal+$tax);

        $this->loop->addTimer(2, function () use ($orderCooked) {
            $this->queue->publish(
                $this->messageFactory->createOrderMessageFromPrevious(OrderPriced::class, $orderCooked)
            );
            $this->ready = true;
        });
    }

    private function calculatePrice(array $ingredients, int $cookTime)
    {
        $price = 0;
        foreach ($ingredients as $ingredient) {
            $price++;
        }

        $price += $cookTime * 4;

        return $price;
    }
}