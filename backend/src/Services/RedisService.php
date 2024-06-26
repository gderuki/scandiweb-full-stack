<?php

namespace Services;

use Redis;
use Services\Interfaces\IRedisService;
use Utils\LogUtils;

class RedisService implements IRedisService
{
    protected $redis;
    protected $logger;

    public function __construct()
    {
        $this->logger = LogUtils::getLogger();

        if (!getenv('REDIS_HOST')) {
            $this->logger->warning('No Redis host found in environment, loading from .env file');

            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
        }

        try {
            $this->redis = new Redis();

            $scheme = getenv('REDIS_SCHEME') ?: 'tcp';
            $host = getenv('REDIS_HOST');
            $port = getenv('REDIS_PORT') ?: 6379;

            if ($scheme === 'tls') {
                $success = $this->redis->connect("tls://$host", $port);
            } else {
                $success = $this->redis->connect($host, $port);
            }

            if (!$success) {
                throw new \RedisException("Unable to connect to Redis: $host:$port");
            }
        } catch (\RedisException $e) {
            $this->logger->error('Failed to connect to Redis: ' . $e->getMessage());
            throw $e;
        }
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
