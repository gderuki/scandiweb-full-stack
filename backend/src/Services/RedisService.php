<?php

namespace Services;

use Services\Interfaces\IRedisService;
use Redis;

class RedisService implements IRedisService
{
    protected $redis;

    public function __construct()
    {
        if (!getenv('REDIS_HOST')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
        }

        $this->redis = new Redis();
        $this->redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    }

    public function set($key, $value)
    {
        $this->redis->set($key, $value);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }
}
