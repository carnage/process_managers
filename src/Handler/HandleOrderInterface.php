<?php

namespace ProcessManagers\Handler;

use ProcessManagers\Model\Order;

interface HandleOrderInterface
{
    public function handle(Order $order);
}