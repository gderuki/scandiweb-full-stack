<?php

require_once '/app/src/Utils/ServiceLocator.php';

use GraphQL\Resolvers\AttributeResolver;
use GraphQL\Resolvers\Interfaces\IAttributeResolver;
use Repositories\AttributeRepository;
use Repositories\CategoryRepository;
use Repositories\Interfaces\ICategoryRepository;
use Repositories\Interfaces\IOrderRepository;
use Repositories\Interfaces\IProductRepository;
use Repositories\OrderRepository;
use Repositories\ProductRepository;
use Services\AttributeService;
use Services\CategoryService;
use Services\Interfaces\IAttributeRepository;
use Services\Interfaces\IAttributeService;
use Services\Interfaces\ICategoryService;
use Services\Interfaces\IOrderService;
use Services\Interfaces\IProductService;
use Services\Interfaces\IRedisService;
use Services\OrderService;
use Services\ProductService;
use Services\RedisService;
use Utils\AppConfig;
use Utils\LogUtils;

// acquire logger
$logger = LogUtils::getLogger();

// register services
$serviceLocator = new ServiceLocator();

// category
$serviceLocator->register(ICategoryRepository::class, function () {
    return new CategoryRepository();
});
$serviceLocator->register(ICategoryService::class, function () use ($serviceLocator, $logger) {
    $categoryRepository = $serviceLocator->get(ICategoryRepository::class);
    return new CategoryService($categoryRepository, $logger);
});

// product
$serviceLocator->register(IProductRepository::class, function () {
    return new ProductRepository();
});
$serviceLocator->register(IProductService::class, function () use ($serviceLocator, $logger) {
    $productRepository = $serviceLocator->get(IProductRepository::class);
    return new ProductService($productRepository, $logger);
});

// attribute repository
$serviceLocator->register(IAttributeRepository::class, function () {
    return new AttributeRepository();
});

// attribute service
$serviceLocator->register(IAttributeService::class, function () use ($serviceLocator, $logger) {
    $attributeRepository = $serviceLocator->get(IAttributeRepository::class);
    return new AttributeService($attributeRepository, $logger);
});

// attribute resolver
$serviceLocator->register(IAttributeResolver::class, function () use ($serviceLocator) {
    return new AttributeResolver($serviceLocator);
});

// Since I'm not hosting dockerized app on AWS, I'm not using Redis in production
if (!AppConfig::isProd()) {
    // redis
    $serviceLocator->register(IRedisService::class, function () {
        return new RedisService();
    });
}

// order repository
$serviceLocator->register(IOrderRepository::class, function () use ($logger) {
    return new OrderRepository($logger);
});

// order service
$serviceLocator->register(IOrderService::class, function () use ($serviceLocator, $logger) {
    $orderRepository = $serviceLocator->get(IOrderRepository::class);
    return new OrderService($orderRepository, $logger);
});

return $serviceLocator;
