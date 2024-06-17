<?php

namespace Controllers;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

use ServiceLocator;
use GraphQL\Types\ProductType;
use GraphQL\Types\CategoryType;
use Services\Interfaces\ICategoryService;
use Services\Interfaces\IProductService;

class GraphQLController
{
    protected static $serviceLocator;

    public static function init(ServiceLocator $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    static public function handle()
    {
        global $serviceLocator;

        try {
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'echo' => [
                        'type' => Type::string(),
                        'args' => [
                            'message' => ['type' => Type::string()],
                        ],
                        'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                    ],
                    'products' => [
                        'type' => Type::listOf(new ProductType()),
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $productService = $serviceLocator->get(IProductService::class);
                            return $productService->populate();
                        },
                    ],
                    'categories' => [
                        'type' => Type::listOf(new CategoryType()),
                        'resolve' => static function ($rootValue, array $args) use ($serviceLocator) {
                            $categoryService = $serviceLocator->get(ICategoryService::class);
                            return $categoryService->populate();
                        },
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
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
