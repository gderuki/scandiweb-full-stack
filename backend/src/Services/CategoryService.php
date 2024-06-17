<?php

namespace Services;

use Repositories\Interfaces\ICategoryRepository;
use Services\BaseService;
use Services\Interfaces\ICategoryService;

/**
 * Service class for managing category data.
 */
class CategoryService extends BaseService implements ICategoryService
{
    public function __construct(ICategoryRepository $categoryRepository)
    {
        $this->repository = $categoryRepository;
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
                '__typename' => 'Category'
            ];
        }, $categories);
    }
}