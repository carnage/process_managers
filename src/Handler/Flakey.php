<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

class Flakey implements HandleMessageInterface
{
    private $handler;

    /**
     * Flakey constructor.
     * @param $handler
     */
    public function __construct(HandleMessageInterface $handler)
    {
        $this->handler = $handler;
    }

    public function handle(MessageInterface $order)
    {
        if (rand(0,1)) {
            $this->handler->handle($order);
        }
        if (rand(0,1)) {
            $this->handler->handle($order);
        }
    }

    public static function getHandleMethod(string $message): string
    {
        return 'handle';
    }

    public function isReady()
    {
        return $this->handler->isReady();
    }
}