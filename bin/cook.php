<?php

use ProcessManagers\Actor\Cook;
use ProcessManagers\Handler\SocketHandler;
use ProcessManagers\Handler\SocketUnhandler;

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$handler = new Cook(new SocketHandler("tcp://assist:10000", $loop), rand(5,10));

new SocketUnhandler($socket, $handler);

$socket->listen(10000, '0.0.0.0');
$loop->run();