<?php

namespace Controllers;

use Decorators\CacheDecorator;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Resolvers\Interfaces\IAttributeResolver;
use GraphQL\Types\Input\ProductInputType;
use GraphQL\Types\Query\AttributeSetType;
use GraphQL\Types\Query\CategoryType;
use GraphQL\Types\Query\ProductType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Utils\TypeRegistry;
use RuntimeException;
use Services\Interfaces\IAttributeService;
use Services\Interfaces\ICategoryService;
use Services\Interfaces\IOrderService;
use Services\Interfaces\IProductService;
use Services\Interfaces\IRedisService;
use Throwable;

class GraphQLController
{
    protected static $serviceLocator;

    public static function init(\ServiceLocator $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    public static function handle()
    {
        global $serviceLocator;

        try {
            $typeRegistry = TypeRegistry::getInstance();

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf($typeRegistry->get('ProductType', function () {
                            return new ProductType();
                        })),
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $cacheDecorator = new CacheDecorator($serviceLocator->get(IRedisService::class));
                            return $cacheDecorator->getOrSet('products_all', static function () use ($serviceLocator) {
                                $productService = $serviceLocator->get(IProductService::class);
                                return $productService->getAll();
                            });
                        },
                    ],
                    'product' => [
                        'type' => $typeRegistry->get('ProductType', function () {
                            return new ProductType();
                        }),
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $cacheDecorator = new CacheDecorator($serviceLocator->get(IRedisService::class));
                            return $cacheDecorator->getOrSet('product_' . $args['id'], static function () use ($serviceLocator, $args) {
                                $productService = $serviceLocator->get(IProductService::class);
                                return $productService->get($args['id']);
                            });
                        },
                    ],
                    'categories' => [
                        'type' => Type::listOf($typeRegistry->get('CategoryType', function () {
                            return new CategoryType();
                        })),
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $cacheDecorator = new CacheDecorator($serviceLocator->get(IRedisService::class));
                            return $cacheDecorator->getOrSet('categories_all', static function () use ($serviceLocator) {
                                $categoryService = $serviceLocator->get(ICategoryService::class);
                                return $categoryService->getAll();
                            });
                        },
                    ],
                    'attributes' => [
                        'type' => Type::listOf($typeRegistry->get('AttributeSetType', function () {
                            return new AttributeSetType();
                        })),
                        'args' => [
                            'productId' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $cacheDecorator = new CacheDecorator($serviceLocator->get(IRedisService::class));
                            $productId = $args['productId'];
                            $cacheKey = "product_attributes_{$productId}";
                            return $cacheDecorator->getOrSet($cacheKey, function () use ($serviceLocator, $productId) {
                                $attributeResolver = $serviceLocator->get(IAttributeResolver::class);
                                return $attributeResolver->resolveAttributes($productId);
                            }, 3600); // 1h
                        },
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'insertOrder' => [
                        'type' => Type::nonNull(Type::boolean()),
                        'args' => [
                            'products' => [
                                'type' => Type::nonNull(Type::listOf(new ProductInputType())),
                            ],
                        ],
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            // simple not-production-ready validation for products and their attributes
                            $productService = $serviceLocator->get(IProductService::class);
                            $products = $args['products'];
                            if (!$productService->validate($products)) {
                                return false;
                            }

                            $attributeService = $serviceLocator->get(IAttributeService::class);
                            if (!$attributeService->validate($products)) {
                                return false;
                            }

                            $orderService = $serviceLocator->get(IOrderService::class);
                            return $orderService->saveOrder($products);
                        },
                    ],
                ],
            ]);

            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
