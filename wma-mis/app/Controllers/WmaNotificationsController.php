<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WmaNotificationModel;

class WmaNotificationsController extends BaseController
{
    private WmaNotificationModel $notifModel;

    public function __construct()
    {
        $this->notifModel = new WmaNotificationModel();
    }

    // ----------------------------------------------------------------
    // Main page — shows all notifications for the logged-in officer
    // ----------------------------------------------------------------
    public function index()
    {
        $userId = auth()->user()->id ?? null;

        $notifications = $userId
            ? $this->notifModel->getForUser((int) $userId)
            : [];

        $data = [
            'page' => [
                'title'   => 'My Notifications',
                'heading' => 'Notifications',
            ],
            'notifications' => $notifications,
        ];

        return view('Pages/Osa/WmaNotifications', $data);
    }

    // ----------------------------------------------------------------
    // AJAX — returns JSON list (used by navbar badge)
    // ----------------------------------------------------------------
    public function getAjax()
    {
        $userId = auth()->user()->id ?? null;

        if (!$userId) {
            return $this->response->setJSON([]);
        }

        $notifications = $this->notifModel->getForUser((int) $userId, 20);
        $unreadCount   = $this->notifModel->countUnread((int) $userId);

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode([
                'notifications' => $notifications,
                'unread_count'  => $unreadCount,
            ]));
    }

    // ----------------------------------------------------------------
    // Mark a single notification as read
    // ----------------------------------------------------------------
    public function markRead($id)
    {
        $userId = auth()->user()->id ?? null;

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Not authenticated']);
        }

        $this->notifModel->markRead((int) $id, (int) $userId);

        return $this->response->setJSON(['success' => true]);
    }

    // ----------------------------------------------------------------
    // Mark ALL notifications as read
    // ----------------------------------------------------------------
    public function markAllRead()
    {
        $userId = auth()->user()->id ?? null;

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Not authenticated']);
        }

        $this->notifModel->markAllRead((int) $userId);

        return $this->response->setJSON(['success' => true]);
    }

    // ----------------------------------------------------------------
    // Internal API — called by the backend (osa_app) when an applicant
    // reuploads a returned document.
    //
    // Expected JSON body:
    // {
    //   "api_key": "wma_internal_notif_key_9x2z",
    //   "application_id": "<uuid>",
    //   "attachment_name": "Practising Certificate",
    //   "returned_by_user_id": 42,
    //   "applicant_name": "Juma Ally",      ← optional
    //   "region_name": "Dar es Salaam",     ← optional
    //   "control_number": "WMA-2026-0012",  ← optional
    //   "request_number": "OSA/REQ/2026/03/01" ← optional
    // }
    // ----------------------------------------------------------------
    public function notifyReupload()
    {
        // Simple shared-secret auth so the endpoint can't be called publicly
        $apiKey = $this->request->getJSON(true)['api_key'] ?? '';
        if ($apiKey !== 'wma_internal_notif_key_9x2z') {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $body = $this->request->getJSON(true);

        $applicationId    = $body['application_id']       ?? null;
        $attachmentName   = $body['attachment_name']       ?? 'hati';
        $returnedByUserId = (int) ($body['returned_by_user_id'] ?? 0);
        $officerEmail     = trim($body['officer_email']    ?? '');
        $applicantName    = $body['applicant_name']        ?? 'Mwombaji';
        $regionName       = $body['region_name']           ?? 'Tanzania';
        $controlNumber    = $body['control_number']        ?? ($applicationId ?? 'N/A');
        $requestNumber    = $body['request_number']        ?? null;

        // ── Resolve correct vessel_discharge user ID via officer email ──────
        // The backend sends the osa_app user ID which differs from vessel_discharge user ID.
        // We bridge using email which is the same across both systems.
        $wmaUserId = null;

        if (!empty($officerEmail)) {
            $db = \Config\Database::connect(); // vessel_discharge (wma-mis default)
            $identity = $db->table('auth_identities')
                           ->select('user_id')
                           ->where('secret', $officerEmail)
                           ->where('type', 'email_password')
                           ->get()->getRow();
            if ($identity) {
                $wmaUserId = (int) $identity->user_id;
            }
        }

        // Fallback to the passed ID if email lookup failed
        if (!$wmaUserId) {
            $wmaUserId = $returnedByUserId ?: null;
        }

        if (!$wmaUserId) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Could not resolve vessel_discharge user ID',
                'officer_email' => $officerEmail,
                'returned_by_user_id' => $returnedByUserId,
            ]);
        }

        // Build the URL for viewing the application attachments tab
        $url = rtrim(base_url(), '/') . '/viewApplication/' . $applicationId . '#documents';
        $attachmentLink = "<a href=\"{$url}\" style=\"text-decoration: underline; color: #0056b3;\" target=\"_blank\">{$attachmentName}</a>";

        // Build the Swahili notification message (HTML supported in preview)
        $title   = 'Nyaraka Ilirejelewa: ' . $attachmentName;
        $message = "Salamu.\n"
            . "Tafadhali fahamu kuwa mwombaji <strong>{$applicantName}</strong> kutoka mkoa wa {$regionName} "
            . "amewasilisha tena nyaraka <strong>'{$attachmentLink}'</strong> baada ya kufanya marekebisho "
            . "kama alivyoshauriwa hapo awali.\n"
            . "Baada ya mapitio ya awali, imebainika kuwa hati hiyo imezingatia maelekezo "
            . "yaliyotolewa na marekebisho yaliyohitajika yamefanyika ipasavyo.\n"
            . "Hivyo, inawasilishwa kwako kwa ajili ya mapitio zaidi na hatua stahiki "
            . "kulingana na utaratibu wa mfumo.\n"
            . "Tafadhali endelea na hatua zinazofuata ipasavyo.\n\nAsante.\n\n"
            . "Maombi Na.: <strong>" . ($requestNumber ?? $controlNumber) . "</strong>";

        $this->notifModel->notify(
            $wmaUserId,
            $title,
            $message,
            'document_reuploaded',
            $applicationId,
            $requestNumber
        );

        return $this->response->setJSON([
            'success'      => true,
            'wma_user_id'  => $wmaUserId,
            'officer_email' => $officerEmail,
        ]);
    }
}
