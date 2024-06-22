<?php

require_once '/app/src/Utils/ServiceLocator.php';

use GraphQL\Resolvers\AttributeResolver;
use GraphQL\Resolvers\Interfaces\IAttributeResolver;
use Repositories\CategoryRepository;
use Repositories\Interfaces\ICategoryRepository;
use Repositories\Interfaces\IProductRepository;
use Repositories\ProductRepository;
use Services\AttributeService;
use Services\CategoryService;
use Services\Interfaces\IAttributeService;
use Services\Interfaces\ICategoryService;
use Services\Interfaces\IProductService;
use Services\Interfaces\IRedisService;
use Services\ProductService;
use Services\RedisService;

// register services
$serviceLocator = new ServiceLocator();

// category
$serviceLocator->register(ICategoryRepository::class, function () {
    return new CategoryRepository();
});
$serviceLocator->register(ICategoryService::class, function () use ($serviceLocator) {
    $categoryRepository = $serviceLocator->get(ICategoryRepository::class);
    return new CategoryService($categoryRepository);
});

// product
$serviceLocator->register(IProductRepository::class, function () {
    return new ProductRepository();
});
$serviceLocator->register(IProductService::class, function () use ($serviceLocator) {
    $productRepository = $serviceLocator->get(IProductRepository::class);
    return new ProductService($productRepository);
});

// attributes
$serviceLocator->register(IAttributeService::class, function () use ($serviceLocator) {
    return new AttributeService($serviceLocator->get(IProductRepository::class));
});

// attribute resolver
$serviceLocator->register(IAttributeResolver::class, function () use ($serviceLocator) {
    return new AttributeResolver($serviceLocator);
});

// redis
$serviceLocator->register(IRedisService::class, function () {
    return new RedisService();
});

return $serviceLocator;
