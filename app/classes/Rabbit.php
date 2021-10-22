<?php

namespace App\Classes;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;

class Rabbit
{
    private const CFG_RABBIT_HOST = 'localhost';

    private const CFG_RABBIT_PORT = 5672;

    private const CFG_RABBIT_USER = 'guest';

    private const CFG_RABBIT_PASSWORD = 'guest';

    private AMQPChannel $channel;

    private AMQPStreamConnection $connection;

    public function __construct()
    {
        try {
            $this->connection = new AMQPStreamConnection(self::CFG_RABBIT_HOST, self::CFG_RABBIT_PORT, self::CFG_RABBIT_USER, self::CFG_RABBIT_PASSWORD);
            $this->channel = $this->connection->channel();
        } catch (AMQPProtocolConnectionException $e) {
            //echo $e->getMessage();
        }
    }

    public function send(string $exchangeName, array $data) : void
    {
        $this->channel->exchange_declare($exchangeName, 'fanout', false, false, false);
        $this->channel->basic_publish(new AMQPMessage(json_encode($data)), $exchangeName);

        echo " [x] Sent message\n";
    }

    public function receive(string $exchangeName) : void
    {
        $this->channel->exchange_declare($exchangeName, 'fanout', false, false, false);

        list($queueName, ,) = $this->channel->queue_declare('', false, false, true, false);

        $this->channel->queue_bind($queueName, $exchangeName);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($data) {
            echo ' [x] ', $data->body, "\n";
        };

        $this->channel->basic_consume($queueName, '', false, true, false, false, $callback);

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
