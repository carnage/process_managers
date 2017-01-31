<?php

namespace ProcessManagers;

class UUID
{
    public function generateIdentity(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(22)), '+/', '-_'), '=');
    }
}