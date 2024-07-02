<?php

namespace Utils;

class AppConfig
{
    private static $instance = null;
    private $settings = [];

    private function __construct()
    {
        $this->settings = [
            'env' => getenv('APP_ENV') ?: 'production',
        ];
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AppConfig();
        }

        return self::$instance;
    }

    public function get($key, $default = null)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }

    private function __clone()
    {}
    public function __wakeup()
    {}

    //region "Utils"
    public static function isProd()
    {
        return self::getInstance()->get('env') === 'production';
    }
    //endregion
}
