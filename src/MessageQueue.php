<?php

namespace ProcessManagers;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\MessageInterface;

class MessageQueue implements PublishInterface
{
    private $subs = [];

    public function publish(MessageInterface $message)
    {
        $messageClass = get_class($message);
        $interfaces = class_implements($messageClass);
        array_unshift($interfaces, $messageClass);

        foreach ($interfaces as $messageType) {
            if (isset($this->subs[$messageType])) {
                foreach ($this->subs[$messageType] as $handler) {
                    /** @var HandleMessageInterface $handler */
                    $handler->handle($message);
                }
            }
        }
    }

    public function subscribe(string $messageType, HandleMessageInterface $handler)
    {
        $method = $handler::getHandleMethod($messageType);
        if (!method_exists($handler, $method)) {
            throw new \LogicException(sprintf('Invalid handler for message %s', $messageType));
        }
        $this->subs[$messageType][] = $handler;
    }
}