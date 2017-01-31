<?php

require __DIR__ . '/../vendor/autoload.php';

use ProcessManagers\Handler\Repeater;
use ProcessManagers\Handler\RoundRobin;
use ProcessManagers\Handler\SocketHandler;

$loop = React\EventLoop\Factory::create();

$cooks = [
    new SocketHandler("tcp://cook1:10000", $loop),
    new SocketHandler("tcp://cook2:10000", $loop),
    new SocketHandler("tcp://cook3:10000", $loop),
];

$repeater = new RoundRobin(...$cooks);

$waiter = new \ProcessManagers\Actor\Waiter($repeater, new \ProcessManagers\UUID());

for ($i = 0; $i<250; $i++) {
    $waiter->placeOrder($i, ['cake']);
}

echo 'done';