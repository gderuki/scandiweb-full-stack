<?php

namespace Services;

use Repositories\Interfaces\IBaseRepository;

/**
 * Serves as a base class for all service classes within the application.
 * Provides common functionality for managing repository instances.
 */
abstract class BaseService
{
    /**
     * @var IBaseRepository Repository instance that implements IBaseRepository.
     */
    protected $repository;

    /**
     * Constructs a new instance of a service.
     *
     * @param IBaseRepository $repository The repository instance that this service will use for data access.
     */
    public function __construct(IBaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Validates the provided data.
     *
     * This method should be overridden by subclasses that require validation.
     * The default implementation throws an exception, indicating that validation is not implemented.
     *
     * @param mixed $data Data to be validated.
     * @throws RuntimeException If validation is not implemented.
     */
    public function validate(?array $data): bool
    {
        throw new RuntimeException("Validation not implemented for " . get_class($this));
    }
}
