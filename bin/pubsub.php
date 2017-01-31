<?php

require __DIR__ . '/../vendor/autoload.php';

//1 waiter => OrderPlaced
//OrderPlaced ~> 3 cooks => OrderCooked
//OrderCooked ~> 1 Assist => OrderPriced
//OrderPriced ~> 1 Cashier

use ProcessManagers\Actor\AssistantManager;
use ProcessManagers\Actor\Cashier;
use ProcessManagers\Actor\Cook;
use ProcessManagers\Actor\Waiter;
use ProcessManagers\Handler\OrderPrinter;
use ProcessManagers\Handler\RoundRobin;
use ProcessManagers\MessageQueue;
use ProcessManagers\UUID;

$messageBus = new MessageQueue();

$cook = new Cook($messageBus, rand(5,10), 'tom');
$cook2 = new Cook($messageBus, rand(5,10), 'harry');
$waiter = new Waiter($messageBus, new UUID());
$assist = new AssistantManager($messageBus);
$cashier = new Cashier($messageBus);
$printer = new OrderPrinter();

$cookHandler = new RoundRobin($cook, $cook2);

$messageBus->subscribe(\ProcessManagers\Topics::ORDER_PLACED, $cookHandler);
$messageBus->subscribe(\ProcessManagers\Topics::ORDER_COOKED, $assist);
$messageBus->subscribe(\ProcessManagers\Topics::ORDER_PRICED, $cashier);
$messageBus->subscribe(\ProcessManagers\Topics::ORDER_PAID, $printer);

for ($i = 0; $i<25; $i++) {
    $waiter->placeOrder($i, ['cake']);
}