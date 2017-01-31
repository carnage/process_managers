<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

class OrderPrinter implements HandleMessageInterface
{
    public function handle(MessageInterface $message)
    {
        if (!method_exists($message, 'getOrder')) {
            return;
        }

        $order = $message->getOrder();

        echo $order->getTableNumber() . ': ' . $order->getOrderId() .' cooked by ' . $order->getCookedBy() . "\n";
    }

    public static function getHandleMethod(string $message): string
    {
        return 'handle';
    }
}
