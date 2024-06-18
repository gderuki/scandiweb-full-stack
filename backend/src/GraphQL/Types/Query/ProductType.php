<?php

namespace GraphQL\Types\Query;

use GraphQL\Resolvers\Interfaces\IAttributeResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct()
    {
        global $serviceLocator;

        $config = [
            'name' => 'Product',
            'description' => 'Represents a product',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The unique ID of the product.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'The name of the product.',
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'The description of the product.',
                ],
                'inStock' => [
                    'type' => Type::boolean(),
                    'description' => 'Whether the product is in stock.',
                ],
                'category' => [
                    'type' => Type::string(),
                    'description' => 'The category of the product.',
                ],
                'brand' => [
                    'type' => Type::string(),
                    'description' => 'The brand of the product.',
                ],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'The gallery images of the product.',
                ],
                'prices' => [
                    'type' => Type::listOf(new PriceItemType()),
                    'description' => 'The price of the product.',
                ],
                'attributes' => [
                    'type' => Type::listOf(new AttributeSetType()),
                    'description' => 'The set of attributes for the product.',
                    'resolve' => function ($rootValue, $args, $context, $info) use ($serviceLocator) {
                        $attributeResolver = $serviceLocator->get(IAttributeResolver::class);

                        $productId = $rootValue['id'];
                        return $attributeResolver->resolveAttributes($productId);
                    },
                ],
                '__typename' => [
                    'type' => Type::string(),
                    'description' => 'The type name of the product.',
                ],
            ],
        ];
        parent::__construct($config);
    }
}
