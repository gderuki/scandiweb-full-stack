<?php

namespace GraphQL\Utils;

class TypeRegistry
{
    private static $instance;
    private $types = [];

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function get($typeName, callable $typeFactory)
    {
        if (!isset($this->types[$typeName])) {
            $this->types[$typeName] = $typeFactory();
        }
        return $this->types[$typeName];
    }

    private function __construct()
    {}
    private function __clone()
    {}
    public function __wakeup()
    {}
}
