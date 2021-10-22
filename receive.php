<?php

require_once(__DIR__ . '/vendor/autoload.php' );
require_once(__DIR__ . '/app/classes/Rabbit.php' );

use App\Classes\Rabbit;

// Receive the message
$rabbitReceiver = new Rabbit;
$rabbitReceiver->receive('TheRealRabbitTester');
