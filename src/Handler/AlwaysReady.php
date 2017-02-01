<?php


namespace ProcessManagers\Handler;


trait AlwaysReady
{
    public function isReady()
    {
        return true;
    }
}