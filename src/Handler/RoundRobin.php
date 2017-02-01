<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

class RoundRobin implements HandleMessageInterface
{
    use AlwaysReady;
    private $handlers;

    public function __construct(HandleMessageInterface ... $handleOrderInterfaces)
    {
        $this->handlers = $handleOrderInterfaces;
    }

    public function handle(MessageInterface $order)
    {
        $handler = array_shift($this->handlers);
        $handler->handle($order);
        array_push($this->handlers, $handler);
    }

    public static function getHandleMethod(string $message): string
    {
        return 'handle';
    }
}