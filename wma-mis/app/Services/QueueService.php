<?php

namespace App\Services;

class QueueService
{
    protected $db;
    protected $config;

    public function __construct()
    {
        $this->db = db_connect();
        $this->config = config('QueueEngine');

    }

    public function push(string $queue, string $job, array $data, string $priority = 'default'): bool
    {
        // Validate queue exists in config
        if (!isset($this->config->queues[$queue])) {
            throw new \RuntimeException("Queue '{$queue}' not configured");
        }

        $payload = json_encode([
            'job' => $job,
            'data' => $data
        ]);

        return $this->db->table('queue_jobs')->insert([
            'queue' => $queue,
            'payload' => $payload,
            'priority' => $priority,
            'status' => 0,
            'attempts' => 0,
            'available_at' => time(),
            'created_at' => time()
        ]);
    }
}
