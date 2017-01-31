<?php

use ProcessManagers\Actor\AssistantManager;
use ProcessManagers\Handler\SocketHandler;
use ProcessManagers\Handler\SocketUnhandler;

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$handler = new AssistantManager(new SocketHandler('tcp://cashier: 10000', $loop));

new SocketUnhandler($socket, $handler);

$socket->listen(10000, '0.0.0.0');
$loop->run();