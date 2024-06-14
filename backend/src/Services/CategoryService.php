<?php

namespace Services;

use Services\BaseService;

/**
 * Service class for managing category data.
 */
class CategoryService extends BaseService
{
    private $categoryRepository;

    public function __construct($categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function populate()
    {
        return $this->getAllCategories();
    }

    private function getAllCategories()
    {
        $categories = $this->categoryRepository->getAll();
        return array_map(function ($category) {
            return [
                'name' => $category['name'],
                '__typename' => 'Category'
            ];
        }, $categories);
    }
}