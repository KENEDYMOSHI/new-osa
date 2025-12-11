<?php

namespace App\Commands;

use App\Libraries\SmsLibrary;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Libraries\WaterMetersLibrary;

class ConsumeQueue extends BaseCommand
{
    protected $group       = 'RabbitMQ';
    protected $name        = 'consume:queue';
    protected $description = 'Consume RabbitMQ queues and process them.';

    protected $queues = [
        'meter_queue',
        'report_verification_queue',
        'invitation_queue'
    ];

    public function run(array $params)
    {
        set_time_limit(120); // Set to 120 seconds
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        foreach ($this->queues as $queueName) {
            // Declare each queue
            $channel->queue_declare($queueName, false, true, false, false);

            $callback = function ($msg) use ($queueName) {
                $queueData = json_decode($msg->body, true);
                CLI::write("Processing queue: $queueName", 'green');

                try {
                    switch ($queueName) {
                        case 'meter_queue':
                            $this->processMeterQueue($queueData);
                            break;
                        case 'report_verification_queue':
                            $this->processReportVerification($queueData);
                            break;
                        case 'invitation_queue':
                            $this->processUserInvitation($queueData);
                            break;
                    }

                    // Acknowledge message after processing
                    $msg->ack();
                } catch (\Exception $e) {
                    // Log the error and do not acknowledge the message
                    CLI::error("Error processing message: " . $e->getMessage());
                }
            };

            // Consume each queue
            $channel->basic_consume($queueName, '', false, false, false, false, $callback);
        }

        // Keep the consumer running
        while ($channel->is_consuming()) {
            $channel->wait();
        }

        // Close connection and channel
        $channel->close();
        $connection->close();
    }

    protected function processMeterQueue($queueData)
    {
        $waterMetersLibrary = new WaterMetersLibrary();
        $waterMetersLibrary->pushMetersToGovesb($queueData);
    }

    protected function processReportVerification($queueData)
    {
        // Handle report verification logic
        CLI::write("Processed report verification: " . json_encode($queueData), 'blue');
    }

    protected function processUserInvitation($queueData)
    {
        // Handle sending user invitation via SMS
        $sms = new SmsLibrary();
        $sms->sendSms($queueData['phoneNumber'], $queueData['message']);
        CLI::write("Processed user invitation: " . json_encode($queueData), 'yellow');
    }
}
