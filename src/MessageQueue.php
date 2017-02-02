<?php

namespace ProcessManagers;

use ProcessManagers\Handler\AbstractMessageHandler;
use ProcessManagers\Handler\HandleMessageInterface;
use ProcessManagers\Message\MessageInterface;

class MessageQueue implements PublishInterface
{
    private $subs = [];
    private $history = [];
    private $verbose = false;

    public function publish(MessageInterface $message)
    {
        $messageClass = get_class($message);
        $interfaces = class_implements($messageClass);
        //array_unshift($interfaces, $message->getCorrId());
        array_unshift($interfaces, $messageClass);

        foreach ($interfaces as $messageType) {
            if ($this->verbose) echo sprintf("Dispatching %s", $messageType);
            if (isset($this->subs[$messageType])) {
                if ($this->verbose) echo sprintf(" found %s handlers", count($this->subs[$messageType]));
                foreach ($this->subs[$messageType] as $handler) {
                    /** @var HandleMessageInterface $handler */
                    $handler->handle($message);
                }
            }
            if ($this->verbose) echo "\n";

        }

        $this->history[$message->getCorrId()][] = $message;
    }

    public function subscribe(string $messageType, HandleMessageInterface $handler)
    {
        $this->subs[$messageType][] = $handler;
    }

    public function getHistory(string $for): array
    {
        return isset($this->history[$for]) ? $this->history[$for] : [];
    }
}