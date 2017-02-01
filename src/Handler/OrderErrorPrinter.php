<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

class OrderErrorPrinter implements HandleMessageInterface
{
    use AlwaysReady;

    public function handle(MessageInterface $message)
    {
        if (!method_exists($message, 'getOrder')) {
            return;
        }

        $order = $message->getOrder();

        echo '***' . $order->getTableNumber() . ': ' . $order->getOrderId() .' cooked twice '. "\n";
    }

    public static function getHandleMethod(string $message): string
    {
        return 'handle';
    }

}
