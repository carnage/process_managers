<?php

use ProcessManagers\Handler\QueueLengthInterface;

$socket = new React\Socket\Server($loop);

$http = new React\Http\Server($socket);
$http->on('request', function ($request, $response) use ($queues) {
    $response->writeHead(200, array('Content-Type' => 'text/html'));
    $string = '';
    foreach ($queues as $queue) {
        /** @var QueueLengthInterface $queue */
        $string .= sprintf("%s: %s \n", $queue->getName(), $queue->getQueueLength());
    }
    $response->end(
        sprintf(
            "
                <pre>%s</pre>
                <script>setTimeout(function () {document.location.reload()}, 1000);</script>
            ",
            $string
        )
    );
});

$socket->listen(1337);