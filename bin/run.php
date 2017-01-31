<?php

include_once __DIR__ . '/../vendor/autoload.php';
$serviceMap = include 'service-map.php';

foreach ($serviceMap as $service) {
    $cmd = "php " . $service['service'] . ' ' . $service['port'] . '&';
    system($cmd);
}