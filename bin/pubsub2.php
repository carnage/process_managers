<?php

require __DIR__ . '/../vendor/autoload.php';

use ProcessManagers\Actor\AssistantManager;
use ProcessManagers\Actor\Cashier;
use ProcessManagers\Actor\Cook;
use ProcessManagers\Actor\Waiter;
use ProcessManagers\Handler\Fair;
use ProcessManagers\Handler\OrderPrinter;
use ProcessManagers\Handler\QueueHandler;
use ProcessManagers\Handler\RoundRobin;
use ProcessManagers\Message\CookFood;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\Message\PriceOrder;
use ProcessManagers\Message\TakePayment;
use ProcessManagers\MessageQueue;
use ProcessManagers\UUID;

$loop = React\EventLoop\Factory::create();

$messageBus = new MessageQueue();
$UUID = new UUID();
$messageFactory = new \ProcessManagers\Message\MessageFactory($UUID);

$cook = new QueueHandler(new Cook($loop, $messageBus, $messageFactory, 1, 'tom'), $loop);
$cook2 = new QueueHandler(new Cook($loop, $messageBus, $messageFactory, 2, 'harry'), $loop);
$waiter = new Waiter($messageBus, $messageFactory, $UUID);
$assist = new QueueHandler(new AssistantManager($loop, $messageBus, $messageFactory), $loop);
$cashier = new QueueHandler(new Cashier($loop, $messageBus, $messageFactory), $loop);
$printer = new OrderPrinter();

$cookHandler = new Fair($loop, $cook, $cook2);

//$messageBus->subscribe(CookFood::class, $cookHandler);
//$messageBus->subscribe(PriceOrder::class, $assist);
//$messageBus->subscribe(TakePayment::class, $cashier);
//$messageBus->subscribe(OrderPaid::class, $printer);

$messageBus->subscribe(OrderPlaced::class, $cookHandler);
$messageBus->subscribe(OrderCooked::class, $assist);
$messageBus->subscribe(OrderPriced::class, $cashier);
$messageBus->subscribe(OrderPaid::class, $printer);

for ($i = 0; $i<20; $i++) {
    $corrId = $waiter->placeOrder($i, ['cake']);
}

$loop->run();