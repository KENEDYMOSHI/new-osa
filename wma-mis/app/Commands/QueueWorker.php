<?php

namespace App\Commands;

use CodeIgniter\CLI\CLI;
use App\Libraries\SmsLibrary;
use CodeIgniter\CLI\BaseCommand;
use App\BackgroundTasks\ControlNumberTask;
use App\Jobs\Payment;

class QueueWorker extends BaseCommand
{
    protected $group       = 'Queue';
    protected $name       = 'queue:work';
    protected $description = 'Process jobs in the queue';

    public function run(array $params)
    {
   
        $queueName = $params[0] ?? 'default';
        CLI::write("Starting queue worker for '{$queueName}' queue...");

        while (true) {
            try {
                // Get the next job from the queue
                
                $db = db_connect();

                $query = $db->table('queue_jobs')
                    ->where('queue', $queueName)
                    ->where('status', 0)
                    ->where('available_at <=', time())
                    ->orderBy('priority', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->limit(1);

                $job = $query->get()->getFirstRow();

                if ($job === null) {
                    CLI::write('No jobs available. Waiting...', 'yellow');
                    sleep(5);
                    continue;
                }

                // Update job status to processing
                $db->table('queue_jobs')
                    ->where('id', $job->id)
                    ->update(['status' => 1, 'attempts' => $job->attempts + 1]);

                // Process the job
                $payload = json_decode($job->payload, true);

                if ($payload['job'] === 'processcontrolnumber') {
                    $controlNumberTask = new ControlNumberTask();
                    $controlNumberTask->processControlNumber($payload['data']);

                }elseif ($payload['job'] === 'processPayment') {
                    $paymentJpb = new Payment();
                    $paymentJpb->processPayment($payload['data']);
                    
                   // $this->processPayment($payload['data']);
                }
                // Mark job as completed
                $db->table('queue_jobs')
                    ->where('id', $job->id)
                    ->update(['status' => 2]);

                CLI::write("Processed job {$job->id} successfully", 'green');
            } catch (\Exception $e) {
                CLI::error("Error processing job: " . $e->getMessage());

                if (isset($job)) {
                    $maxAttempts = 3;
                    if ($job->attempts >= $maxAttempts) {
                        // Log the failed job
                        $db->table('queue_jobs_failed')->insert([
                            'queue'        => $job->queue,
                            'payload'      => $job->payload,
                            'attempts'     => $job->attempts,
                            'failed_at'    => date('Y-m-d H:i:s'),
                            'error'        => $e->getMessage(),
                        ]);

                        // Delete the job from the main queue
                        $db->table('queue_jobs')->where('id', $job->id)->delete();
                    } else {
                        // Reset status to allow retry
                        $db->table('queue_jobs')
                            ->where('id', $job->id)
                            ->update(['status' => 0]);
                    }
                }
            }

            // Small delay between jobs
            usleep(250000); // 0.25 seconds
        }
    }

   
   


   


    public function processPayment(array $data){
        $faker = \Faker\Factory::create();

        $xml = $data['payments'];

        echo($xml);
       

       // file_put_contents('billXml.xml',print_r($data));

        $fullName = 'Payer: '.$faker->name;
        $sms = new SmsLibrary();
        $url = base_url();
       // $sms->sendSms('0659851709', 'Payment is received  from '.$fullName); 
    }
}
