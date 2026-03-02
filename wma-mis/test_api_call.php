<?php
$apiUrl = 'http://localhost:8080/api/approval/dashboard/osa-stats';
$apiKey = 'osa_approval_api_key_12345';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-KEY: ' . $apiKey,
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);
echo "HTTP Code: $httpCode\n";
echo "Error: $error\n";
echo "Response: " . substr($response, 0, 200) . "\n";
