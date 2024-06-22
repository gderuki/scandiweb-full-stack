<?php

namespace Services;

use Psr\Log\LoggerInterface;
use Repositories\Interfaces\IBaseRepository;
use Services\BaseService;

/*
 * Base class for all services that use a repository.
 */
class RepositoryService extends BaseService
{
    /**
     * @var IBaseRepository Repository instance that implements IBaseRepository.
     */
    protected $repository;

    /**
     * Constructs a new instance of a service with repository.
     *
     * @param IBaseRepository $repository The repository instance that this service will use for data access.
     */
    public function __construct(IBaseRepository $repository, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }
}
