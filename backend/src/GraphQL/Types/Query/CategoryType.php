<?php

namespace GraphQL\Types\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Category',
            'description' => 'Represents a product',
            'fields' => [
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The unique ID of the product.',
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
