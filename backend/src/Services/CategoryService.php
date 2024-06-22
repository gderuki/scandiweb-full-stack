<?php

namespace Services;

use Psr\Log\LoggerInterface;
use Repositories\Interfaces\ICategoryRepository;
use Services\Interfaces\ICategoryService;
use Services\RepositoryService;

/**
 * Service class for managing category data.
 */
class CategoryService extends RepositoryService implements ICategoryService
{
    public function __construct(ICategoryRepository $categoryRepository, LoggerInterface $logger)
    {
        parent::__construct($categoryRepository, $logger);
        $this->repository = $categoryRepository;

        $this->logger->info('Instance created', ['class' => get_class($this)]);
    }

    public function populate()
    {
        return $this->getAllCategories();
    }

    private function getAllCategories()
    {
        $categories = $this->repository->getAll();
        return array_map(function ($category) {
            return [
                'name' => $category['name'],
                '__typename' => 'Category',
            ];
        }, $categories);
    }
}
