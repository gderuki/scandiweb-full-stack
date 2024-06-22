<?php

namespace Services;

use Repositories\Interfaces\IBaseRepository;
use Psr\Log\LoggerInterface;

/**
 * Serves as a base class for all service classes within the application.
 */
abstract class BaseService
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
