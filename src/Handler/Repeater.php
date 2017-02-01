<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Model\Order;

class Repeater implements HandleMessageInterface
{
    use AlwaysReady;
    private $handlers;

    public function __construct(HandleMessageInterface ...$handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
    }

    public function handle(MessageInterface $order)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($order);
        }
    }

    public static function getHandleMethod(string $message): string
    {
        return 'handle';
    }
}