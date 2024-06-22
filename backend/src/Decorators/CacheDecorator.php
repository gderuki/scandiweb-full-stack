<?php

namespace Decorators;

use Services\Interfaces\IRedisService;

class CacheDecorator
{
    private $redisService;
    private $expirationTime;

    public function __construct(IRedisService $redisService, $expirationTime = 3600)
    {
        $this->redisService = $redisService;
        $this->expirationTime = $expirationTime;
    }

    public function getOrSet($cacheKey, callable $fetchFunction)
    {
        $cachedData = $this->redisService->get($cacheKey);
        if ($cachedData !== false) {
            return json_decode($cachedData, true);
        }

        $data = $fetchFunction();
        $this->redisService->set($cacheKey, json_encode($data), $this->expirationTime);
        return $data;
    }
}
