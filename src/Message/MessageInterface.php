<?php

namespace ProcessManagers\Message;

interface MessageInterface
{
    public function getId();
    public function getCorrId();
    public function getCauseId();
}