<?php


namespace ProcessManagers;


use ProcessManagers\Message\MessageInterface;

interface PublishInterface
{
    public function publish(MessageInterface $message);
}