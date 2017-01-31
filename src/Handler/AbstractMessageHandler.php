<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

abstract class AbstractMessageHandler implements HandleMessageInterface
{
    public function handle(MessageInterface $message)
    {
        $method = self::getHandleMethod(get_class($message));
        $this->$method($message);
    }

    public static function getHandleMethod(string $message):string
    {
        $classParts = explode('\\', $message);
        return 'handle' . end($classParts);
    }
}