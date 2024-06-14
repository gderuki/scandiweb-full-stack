<?php

require_once '/app/src/ServiceLocator/ServiceLocator.php';

require_once '/app/src/Repositories/Interfaces/ICategoryRepository.php';
require_once '/app/src/Repositories/CategoryRepository.php';

require_once '/app/src/Repositories/Interfaces/IProductRepository.php';
require_once '/app/src/Repositories/ProductRepository.php';


use Repositories\CategoryRepository;
use Repositories\Interfaces\ICategoryRepository;

use Repositories\ProductRepository;
use Repositories\Interfaces\IProductRepository;

// register services
$serviceLocator = new ServiceLocator();

// category
$serviceLocator->register(ICategoryRepository::class, function() {
    return new CategoryRepository();
});

// product
$serviceLocator->register(IProductRepository::class, function() {
    return new ProductRepository();
});

return $serviceLocator;