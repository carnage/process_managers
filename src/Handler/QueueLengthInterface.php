<?php


namespace ProcessManagers\Handler;


interface QueueLengthInterface
{
    public function getQueueLength(): int;
    public function getName(): string;
}