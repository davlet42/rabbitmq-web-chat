<?php

require_once(__DIR__ . '/vendor/autoload.php' );
require_once(__DIR__ . '/app/classes/Rabbit.php' );

use App\Classes\Rabbit;

// Send the message
$rabbitSender = new Rabbit;

$data = [
    'type' => 'reload',
    'body' => true
];

$rabbitSender->send('TheRealRabbitTester', $data);
