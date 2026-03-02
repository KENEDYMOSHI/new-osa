<?php

namespace App\Models;

use CodeIgniter\Model;

class WmaNotificationModel extends Model
{
    protected $table      = 'wma_notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'title',
        'message',
        'type',
        'related_entity_id',
        'request_number',
        'is_read',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Insert a notification for a single user.
     */
    public function notify(int $userId, string $title, string $message, string $type = 'info', ?string $relatedEntityId = null, ?string $requestNumber = null): bool
    {
        return $this->insert([
            'user_id'           => $userId,
            'title'             => $title,
            'message'           => $message,
            'type'              => $type,
            'related_entity_id' => $relatedEntityId,
            'request_number'    => $requestNumber,
            'is_read'           => 0,
        ]) !== false;
    }

    /**
     * Insert a notification for multiple users (e.g. all surveillance officers).
     */
    public function notifyMany(array $userIds, string $title, string $message, string $type = 'info', ?string $relatedEntityId = null, ?string $requestNumber = null): void
    {
        foreach ($userIds as $userId) {
            $this->notify((int) $userId, $title, $message, $type, $relatedEntityId, $requestNumber);
        }
    }

    /**
     * Get all notifications for a specific user, newest first.
     */
    public function getForUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Count unread notifications for a user.
     */
    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Mark a specific notification as read (only if it belongs to the given user).
     */
    public function markRead(int $id, int $userId): bool
    {
        return $this->where('id', $id)
                    ->where('user_id', $userId)
                    ->set(['is_read' => 1])
                    ->update() !== false;
    }

    /**
     * Mark all notifications for a user as read.
     */
    public function markAllRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->set(['is_read' => 1])
                    ->update() !== false;
    }
}
