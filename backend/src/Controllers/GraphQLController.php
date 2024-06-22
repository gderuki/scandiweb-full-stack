<?php

namespace Controllers;

use Decorators\CacheDecorator;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Types\Input\AttributeSetInputType;
use GraphQL\Types\Query\CategoryType;
use GraphQL\Types\Query\ProductType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Services\Interfaces\IAttributeService;
use Services\Interfaces\ICategoryService;
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
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf(new ProductType()),
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $cacheDecorator = new CacheDecorator($serviceLocator->get(IRedisService::class));
                            return $cacheDecorator->getOrSet('products_all', static function () use ($serviceLocator) {
                                $productService = $serviceLocator->get(IProductService::class);
                                return $productService->populate();
                            });
                        },
                    ],
                    'categories' => [
                        'type' => Type::listOf(new CategoryType()),
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $cacheDecorator = new CacheDecorator($serviceLocator->get(IRedisService::class));
                            return $cacheDecorator->getOrSet('categories_all', static function () use ($serviceLocator) {
                                $categoryService = $serviceLocator->get(ICategoryService::class);
                                return $categoryService->populate();
                            });
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
                                'type' => Type::nonNull(Type::listOf(new InputObjectType([
                                    'name' => 'ProductInput',
                                    'fields' => [
                                        'productId' => ['type' => Type::nonNull(Type::string())],
                                        'attributes' => ['type' => Type::listOf(new AttributeSetInputType())],
                                    ],
                                ]))),
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

                            return true;
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
