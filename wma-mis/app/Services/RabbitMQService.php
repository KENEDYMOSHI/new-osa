<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        // Create a connection to RabbitMQ
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
    }

    public function declareQueue($queueName)
    {
        // Declare a queue with the specified name
        $this->channel->queue_declare($queueName, false, true, false, false);
    }

    public function publish($data, $queueName)
    {
        // Declare the queue before publishing (in case it's not already declared)
        $this->declareQueue($queueName);

        // Convert data to JSON
        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]); // Make message persistent

        // Publish message to the specified queue
        $this->channel->basic_publish($msg, '', $queueName);
    }

    public function close()
    {
        // Close the channel and connection
        $this->channel->close();
        $this->connection->close();
    }
}
