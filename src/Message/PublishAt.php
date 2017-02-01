<?php

namespace ProcessManagers\Message;

class PublishAt extends AbstractMessage
{
    private $ttl;
    private $message;

    /**
     * PublishAt constructor.
     * @param $ttl
     * @param $message
     */
    public function __construct(string $id, string $causeId, string $corrId, int $ttl, MessageInterface $message)
    {
        parent::__construct($id, $causeId, $corrId);
        $this->ttl = $ttl;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }
}