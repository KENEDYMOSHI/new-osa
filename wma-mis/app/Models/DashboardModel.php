<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    // Using API instead of direct DB access
    private $apiUrl = 'http://localhost:8080/api/dashboard';
    private $apiKey = 'osa_approval_api_key_12345'; // TODO: Move to .env

    private function fetchApi($endpoint, $params)
    {
        $ch = curl_init($this->apiUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['params' => $params]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response);
        }

        return [];
    }

    public function vtv($params)
    {
        return $this->fetchApi('vtv', $params);
    }

    public function sbl($params)
    {
        return $this->fetchApi('sbl', $params);
    }

    public function waterMeters($params)
    {
        return $this->fetchApi('water-meters', $params);
    }

    public function ppg($params)
    {
        // Pass PrepackageGfsCode from settings if valid
        if (!isset($params['PrepackageGfsCode']) && function_exists('setting')) {
             $params['PrepackageGfsCode'] = setting('Gfs.prePackages');
        }
        return $this->fetchApi('ppg', $params);
    }

    public function others($params)
    {
        return $this->fetchApi('others', $params);
    }
}