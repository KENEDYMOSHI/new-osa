<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class NotificationController extends ResourceController
{
    use ResponseTrait;

    private function getUserFromToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        } else {
            return null;
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUserNotifications()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid token');
        }

        $userId = $user->uid ?? $user->id ?? null;
        if (!$userId) {
            return $this->failUnauthorized('User ID not found in token');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('notifications');
        $builder->where('user_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        $builder->limit(50); // Limit to last 50 notifications
        
        $notifications = $builder->get()->getResultArray();

        return $this->respond($notifications);
    }

    public function markAsRead($id)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid token');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('notifications');
        
        // Ensure the notification belongs to this user
        $builder->where('id', $id);
        $builder->where('user_id', $user->uid);
        
        $builder->update([
            'is_read' => true,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->respond(['message' => 'Notification marked as read']);
    }
}
