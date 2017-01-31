<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Message\MessageInterface;

interface HandleMessageInterface
{
    public function handle(MessageInterface $message);
    public static function getHandleMethod(string $message): string;
}