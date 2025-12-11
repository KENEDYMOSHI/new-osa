<?php namespace App\Libraries;

class Text {
    function sendSms() {
        // Set the URL
        $url = 'http://msdg.ega.go.tz/msdg/public/quick_sms';

        // Define the POST data
        $data = json_encode([
            'recipients' => '255659851709',
            'message' => 'Hello World',
            'datetime' => date('Y-m-dH:i:s'),
            'mobile_service_id' => '952',
            'sender_id' => '15200',
        ]);

        // Calculate the payload hash
        $apiKey = '8MHsyV3UkU0Qet9RnK7nzJDUxSoJF5EgP1iyHDM6';
        $hash = hash('sha256', $data . $apiKey);
        $payload = base64_encode($hash);

        // Set the headers
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'X-Auth-Request-Hash: ' . $payload,
            'X-Auth-Request-Id: rehema.michael@wma.go.tz',
            'X-Auth-Request-Type: api',
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            // Handle cURL error
            return 'cURL Error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Check if the request was successful
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode === 200) {
            // Handle the response data
            return $response;
        } else {
            // Handle the error
            return 'Request failed with status code: ' . $httpCode;
        }
    }
}

// Usage

?>
