<?php

namespace Utils;

class RateLimiter
{
    protected $redis;
    protected $limit;
    protected $window;

    public function __construct($redis, $limit = 100, $window = 3600)
    {
        $this->redis = $redis;
        $this->limit = $limit;
        $this->window = $window;
    }

    public function isLimited($identifier)
    {
        $key = $this->getKey($identifier);
        $current = $this->redis->get($key);

        if ($current !== false && $current >= $this->limit) {
            return true;
        }

        if ($current === false) {
            $this->redis->setex($key, $this->window, 1);
        } else {
            $this->redis->incr($key);
        }

        return false;
    }

    protected function getKey($identifier)
    {
        $windowId = floor(time() / $this->window);
        return "rate_limit:{$identifier}:{$windowId}";
    }
}
