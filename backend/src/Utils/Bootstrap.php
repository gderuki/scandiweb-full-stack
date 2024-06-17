<?php

require_once '/app/src/ServiceLocator/ServiceLocator.php';

require_once '/app/src/Repositories/Interfaces/ICategoryRepository.php';
require_once '/app/src/Repositories/CategoryRepository.php';

require_once '/app/src/Repositories/Interfaces/IProductRepository.php';
require_once '/app/src/Repositories/ProductRepository.php';


use Repositories\CategoryRepository;
use Repositories\Interfaces\ICategoryRepository;
use Services\ProductService;

use Repositories\ProductRepository;
use Repositories\Interfaces\IProductRepository;
use Services\CategoryService;
use Services\Interfaces\ICategoryService;
use Services\Interfaces\IProductService;

// register services
$serviceLocator = new ServiceLocator();

// category
$serviceLocator->register(ICategoryRepository::class, function() {
    return new CategoryRepository();
});
$serviceLocator->register(ICategoryService::class, function() use ($serviceLocator) {
    $categoryRepository = $serviceLocator->get(ICategoryRepository::class);
    return new CategoryService($categoryRepository);
});

// product
$serviceLocator->register(IProductRepository::class, function() {
    return new ProductRepository();
});
$serviceLocator->register(IProductService::class, function() use ($serviceLocator) {
    $productRepository = $serviceLocator->get(IProductRepository::class);
    return new ProductService($productRepository);
});

return $serviceLocator;