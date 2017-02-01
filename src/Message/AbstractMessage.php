<?php

namespace ProcessManagers\Message;

abstract class AbstractMessage implements MessageInterface
{
    private $id;
    private $causeId;
    private $corrId;

    /**
     * AbstractMessage constructor.
     * @param $causeId
     * @param $corrId
     */
    public function __construct(string $id, string $causeId, string $corrId)
    {
        $this->causeId = $causeId;
        $this->corrId = $corrId;
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCauseId()
    {
        return $this->causeId;
    }

    /**
     * @return mixed
     */
    public function getCorrId()
    {
        return $this->corrId;
    }
}