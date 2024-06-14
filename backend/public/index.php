<?php

require '/app/vendor/autoload.php';
require '/app/src/Utils/Bootstrap.php';
require_once '/app/src/Services/ProductService.php';
require_once '/app/src/Services/CategoryService.php';

use Repositories\Interfaces\IProductRepository;
use Repositories\Interfaces\ICategoryRepository;

use Services\ProductService;
use Services\CategoryService;

$productRepository = $serviceLocator->get(IProductRepository::class);
$productService = new ProductService($productRepository);
$products = $productService->populate();

$categoryRepository = $serviceLocator->get(ICategoryRepository::class);
$categoryService = new CategoryService($categoryRepository);
$categories = $categoryService->populate();

$response = [
    'data' => [
        'categories' => $categories,
        'products' => $products,
    ],
];

header('Content-Type: application/json');
echo json_encode($response);