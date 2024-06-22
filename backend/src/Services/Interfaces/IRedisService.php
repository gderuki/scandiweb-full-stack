<?php

namespace Services\Interfaces;

interface IRedisService
{
    public function set($key, $value);
    public function get($key);
}
