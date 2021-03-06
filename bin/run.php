<?php

require __DIR__ . '/../vendor/autoload.php';

use ProcessManagers\Actor\AlarmClock;
use ProcessManagers\Actor\AssistantManager;
use ProcessManagers\Actor\Cashier;
use ProcessManagers\Actor\Cook;
use ProcessManagers\Actor\OrderStates\OrderStateFactory;
use ProcessManagers\Actor\Runner;
use ProcessManagers\Actor\Waiter;
use ProcessManagers\Handler\Fair;
use ProcessManagers\Handler\Flakey;
use ProcessManagers\Handler\OrderErrorPrinter;
use ProcessManagers\Handler\OrderPrinter;
use ProcessManagers\Handler\QueueHandler;
use ProcessManagers\Handler\RoundRobin;
use ProcessManagers\Handler\TtlQueue;
use ProcessManagers\Message\CookFood;
use ProcessManagers\Message\MessageInterface;
use ProcessManagers\Message\OrderCooked;
use ProcessManagers\Message\OrderCookedTwice;
use ProcessManagers\Message\OrderPaid;
use ProcessManagers\Message\OrderPlaced;
use ProcessManagers\Message\OrderPriced;
use ProcessManagers\Message\OrderSpiked;
use ProcessManagers\Message\PriceOrder;
use ProcessManagers\Message\PublishAt;
use ProcessManagers\Message\TakePayment;
use ProcessManagers\MessageQueue;
use ProcessManagers\UUID;

$loop = React\EventLoop\Factory::create();

$messageBus = new MessageQueue();
$UUID = new UUID();
$messageFactory = new \ProcessManagers\Message\MessageFactory($UUID);

$waiter = new Waiter($messageBus, $messageFactory, $UUID);

$cook = new QueueHandler('Tom', new Flakey(new Cook($loop, $messageBus, $messageFactory, 2, 'tom')), $loop);
$cook2 = new QueueHandler('Harry', new Flakey(new Cook($loop, $messageBus, $messageFactory, 4, 'harry')), $loop);
$cook3 = new QueueHandler('Rich', new Flakey(new Cook($loop, $messageBus, $messageFactory, 6, 'rich')), $loop);
$cookHandler = new Fair('Cooks', $loop, $cook, $cook2, $cook3);

$assist = new QueueHandler('Assist1', new AssistantManager($loop, $messageBus, $messageFactory), $loop);
$assist2 = new QueueHandler('Assist2', new AssistantManager($loop, $messageBus, $messageFactory), $loop);
$assistHandler = new Fair('Assists', $loop, $assist, $assist2);

$cashier = new QueueHandler('Cashier', new Cashier($loop, $messageBus, $messageFactory), $loop);

$runner = new Runner('Runner', $messageBus, new OrderStateFactory($messageBus, $messageFactory));

$alarmClock = new AlarmClock($loop, $messageBus);

$queues =[
    $cook,
    $cook2,
    $cook3,
    $cookHandler,
    $assist,
    $assist2,
    $assistHandler,
    $cashier,
    $runner
];

$printer = new OrderPrinter();
$doubleCook = new OrderErrorPrinter();

$messageBus->subscribe(CookFood::class, $cookHandler);
$messageBus->subscribe(PriceOrder::class, $assistHandler);
$messageBus->subscribe(TakePayment::class, $cashier);
$messageBus->subscribe(OrderSpiked::class, $printer);
$messageBus->subscribe(PublishAt::class, $alarmClock);
$messageBus->subscribe(OrderCookedTwice::class, $doubleCook);


$messageBus->subscribe(MessageInterface::class, $runner);


include_once 'status.php';

for ($i=0; $i<30; $i++) {
    $waiter->placeOrder($i, ['cake']);
}

//include_once 'place-order.php';

$loop->run();