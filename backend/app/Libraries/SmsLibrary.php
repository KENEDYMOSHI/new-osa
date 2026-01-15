<?php

namespace App\Libraries;

/**
 * SmsLibrary Class
 * 
 * This class is responsible for sending SMS messages using an external API.
 */
class SmsLibrary
{
    /**
     * Send an SMS message to a recipient
     *
     * @param string $recipient The phone number of the recipient
     * @param string $message The message content to be sent
     * @return string|void Returns an empty string if the request times out, or void otherwise
     */
    public function sendSms($recipient, $message)
    {
  
        try {
            // Initialize the cURL request service
 
            
            $client = \Config\Services::curlrequest();

            // API endpoint for sending SMS
           // $url = 'http://msdg.ega.go.tz/msdg/public/quick_sms';
            $url = 'https://mgov.gov.go.tz/gateway/sms/quick_sms';

            // Prepare the data payload
            $data = json_encode([
                'recipients' => $recipient,
                'message' => $message,
                'datetime' => date('Y-m-d H:i:s'),
                'mobile_service_id' => '954',
                'sender_id' => '15200',
                // 'sender_id' => 'VIPIMO',
            ]);

            // Generate the authentication hash
            $apiKey = 'HKudePWZRwfj0Kqy00VW4X2ZRMomZY9SaKQTLjSu';
            $hash = hash_hmac('sha256', $data, $apiKey, true);
            $base64Hash = base64_encode($hash);

            // Set up the request options
            $options = [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'X-Auth-Request-Hash' => $base64Hash,
                    'X-Auth-Request-Id' => 'rehema.michael@wma.go.tz',
                    'X-Auth-Request-Type' => 'api',
                ],
                'body' => http_build_query([
                    'data' => $data,
                    'datetime' => date('Y-m-d H:i:s')
                ]),
            ];

            // Set maximum execution time to 35 seconds
            set_time_limit(35);

            // Send the request and measure response time
            $startTime = microtime(true);
            $response = $client->request('POST', $url, $options);
            $responseTime = microtime(true) - $startTime;

            // Check if the request exceeded 30 seconds
            if ($responseTime > 30) {
                return '';
            }

            // Get the response body
            $responseBody = $response->getBody();

            // Check if the request was successful (status code 200)
            if ($response->getStatusCode() === 200) {
                // Success handling (currently commented out)
                 return $responseBody;
            } else {
                // Error handling (currently commented out)
                // return 'Request failed with status code: ' . $response->getStatusCode();
            }
        } catch (\Throwable $th) {
            // Exception handling
            $response = [
                'status' => 0,
                'code' => 501,
                'msg' => $th->getMessage(),
            ];
        }
        // Return response (currently commented out)
        return json_encode($response);
    }
}
