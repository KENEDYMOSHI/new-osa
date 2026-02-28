<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class UserNotificationsController extends BaseController
{
    public function index()
    {
        $userId = auth()->user()->id ?? null;

        $notifications = [];

        if ($userId) {
            // Query the osa_app notifications table directly; this is the backend DB
            $db = \Config\Database::connect('osa');
            $notifications = $db->table('notifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->get()
                ->getResult();
        }

        $data = [
            'page' => [
                'title' => 'My Notifications'
            ],
            'notifications' => $notifications
        ];

        return view('Pages/Osa/Notifications', $data);
    }

    public function getNotificationsAjax()
    {
        $userId = auth()->user()->id ?? null;

        if (!$userId) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect('osa');
        $notifications = $db->table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        return $this->response->setContentType('application/json')->setBody(json_encode($notifications));
    }

    public function markNotificationRead($id)
    {
        $userId = auth()->user()->id ?? null;

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Not authenticated']);
        }

        $db = \Config\Database::connect('osa');
        $db->table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'is_read' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return $this->response->setJSON(['success' => true]);
    }
}

