<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class UserNotificationsController extends BaseController
{
    public function index()
    {
        $token = session()->get('token');
        
        // Fetch notifications from backend API
        $url = 'http://localhost:8080/api/notifications'; // Using the backend route we found earlier
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $notifications = [];
        if ($httpCode === 200) {
            $notifications = json_decode($response);
        }

        $data = [
            'page' => [
                'title' => 'My Notifications'
            ],
            'notifications' => $notifications
        ];

        return view('Pages/Osa/Notifications', $data);
    }
}
