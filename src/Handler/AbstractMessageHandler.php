<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

abstract class AbstractMessageHandler implements HandleMessageInterface
{
    protected $ready = true;

    public function handle(MessageInterface $message)
    {
        $method = static::getHandleMethod(get_class($message));
        if (!method_exists($this, $method)) {
            return;
        }
        $this->$method($message);
        $this->ready = false;
    }

    public static function getHandleMethod(string $message):string
    {
        $classParts = explode('\\', $message);
        return 'handle' . end($classParts);
    }

    public function isReady()
    {
        return $this->ready;
    }
}