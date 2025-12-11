<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RateLimiter implements FilterInterface
{
    protected $maxRequests = 120;
    protected $timeWindow = 60; // 1 minute
    protected $blockDuration = 300; // 5 minutes (in seconds)

    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();
        $cache = \Config\Services::cache();
        $key = "rate_limit_" . md5($ip);
        $blockKey = "blocked_ip_" . md5($ip);
       

        // Check if IP is currently blocked
        if ($cache->get($blockKey)) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(view('Blocked'));
        }

        $data = $cache->get($key);
        if ($data === null) {
            $data = [
                'count' => 1,
                'start_time' => time()
            ];
        } else {
            $elapsed = time() - $data['start_time'];
            if ($elapsed <= $this->timeWindow) {
                $data['count']++;
            } else {
                // Reset window
                $data['count'] = 1;
                $data['start_time'] = time();
            }
        }

        // Save data for next request
        $cache->save($key, $data, $this->timeWindow + 10); // Add buffer time to ensure record is available

        // Check if rate limit is exceeded
        if ($data['count'] > $this->maxRequests) {
            // Block this IP
            $cache->save($blockKey, true, $this->blockDuration);
            
            return service('response')
                ->setStatusCode(429)
                ->setBody(view('Blocked'));
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
        return $response;
    }
}