<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * Handles the /notifications page for WMA-MIS users (officers & applicants).
 * Reads from vessel_discharge.wma_notifications using the Shield user_id directly.
 */
class UserNotificationsController extends BaseController
{
    // ── Page ──────────────────────────────────────────────────────────
    public function index()
    {
        $userId        = auth()->user()->id ?? null;
        $notifications = [];

        if ($userId) {
            $db = \Config\Database::connect(); // vessel_discharge (default)
            $notifications = $db->table('wma_notifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->get()
                ->getResult(); // stdClass objects — required by view
        }

        return view('Pages/Osa/Notifications', [
            'page'          => ['title' => 'My Notifications'],
            'notifications' => $notifications,
        ]);
    }

    // ── AJAX (navbar badge) ───────────────────────────────────────────
    public function getNotificationsAjax()
    {
        $userId = auth()->user()->id ?? null;
        if (!$userId) return $this->response->setJSON([]);

        $db   = \Config\Database::connect();
        $rows = $db->table('wma_notifications')
                   ->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->limit(50)
                   ->get()
                   ->getResultArray();

        return $this->response->setContentType('application/json')->setBody(json_encode($rows));
    }

    // ── Mark one read ─────────────────────────────────────────────────
    public function markNotificationRead($id)
    {
        $userId = auth()->user()->id ?? null;
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Not authenticated']);
        }

        $db = \Config\Database::connect();
        $db->table('wma_notifications')
           ->where('id', $id)
           ->where('user_id', $userId)
           ->update(['is_read' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['success' => true]);
    }

    // ── Mark all read ─────────────────────────────────────────────────
    public function markAllRead()
    {
        $userId = auth()->user()->id ?? null;
        if (!$userId) {
            return redirect()->back()->with('error', 'Not authenticated');
        }

        $db = \Config\Database::connect();
        $db->table('wma_notifications')
           ->where('user_id', $userId)
           ->where('is_read', 0)
           ->update(['is_read' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
