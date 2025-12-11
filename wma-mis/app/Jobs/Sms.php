<?php
namespace App\Jobs;

use App\Libraries\SmsLibrary;
use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;

class Sms extends BaseJob implements JobInterface
{
    protected int $retryAfter = 60;  // Retry after 60 seconds if failed
    protected int $tries = 1;        // Number of attempts

    public function process()
    {
        $params = $this->data['params'];
        $message = $params['message'];
        $phoneNumber = $params['phoneNumber'];

        $smsLib = new SmsLibrary();
        
        try {
            $result = $smsLib->sendSms($phoneNumber, $message);
            
            // Log success if needed
            log_message('info', "SMS sent successfully to {$phoneNumber}");
            
            return $result;
        } catch (\Exception $e) {
            // Log error
            log_message('error', "Failed to send SMS to {$phoneNumber}: " . $e->getMessage());
            
            // Throwing the exception will cause the job to be retried if tries > 1
            throw $e;
        }
    }
}
