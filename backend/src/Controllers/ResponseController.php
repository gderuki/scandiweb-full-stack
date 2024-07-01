<?php

namespace Controllers;

use Services\ProductService;
use Services\CategoryService;

/*
* @deprecated direct HTTP GET access (disabled by default)
*/
class ResponseController
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function getProductsAndCategories()
    {
        $products = $this->productService->getAll();
        $categories = $this->categoryService->getAll();

        $response = [
            'data' => [
                'categories' => $categories,
                'products' => $products,
            ],
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
