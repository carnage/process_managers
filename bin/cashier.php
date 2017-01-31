<?php

use ProcessManagers\Actor\AssistantManager;
use ProcessManagers\Actor\Cashier;
use ProcessManagers\Handler\OrderPrinter;
use ProcessManagers\Handler\SocketUnhandler;

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$handler = new Cashier(new OrderPrinter());

new SocketUnhandler($socket, $handler);

$socket->listen(10000, '0.0.0.0');
$loop->run();