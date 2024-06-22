<?php

namespace Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogUtils
{
    private static $logger;

    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger('app');
            $projectRoot = dirname(__DIR__, 2);
            self::$logger->pushHandler(new StreamHandler($projectRoot . '/Logs/app.log', Logger::DEBUG));
        }

        return self::$logger;
    }
}
