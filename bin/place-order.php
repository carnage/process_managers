<?php
$socket = new React\Socket\Server($loop);

$t = 0;
$http = new React\Http\Server($socket);
$http->on('request', function ($request, $response) use ($waiter, &$t) {
    $response->writeHead(200, array('Content-Type' => 'text/html'));
    $response->end(
        sprintf(
            "<link rel=\"shortcut icon\" href=\"data:image/x-icon;,\" type=\"image/x-icon\"> Order %s placed",
            $waiter->placeOrder($t, ['cake'])
        )
    );
    $t++;
});

$socket->listen(1338);