<?php

namespace GraphQL\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class ProductInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'ProductInput',
            'fields' => [
                'productId' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'quantity' => [
                    'type' => Type::nonNull(Type::int()),
                ],
                'attributes' => [
                    'type' => Type::listOf(new InputObjectType([
                        'name' => 'AttributeInput',
                        'fields' => [
                            'key' => ['type' => Type::nonNull(Type::string())],
                            'value' => ['type' => Type::nonNull(Type::string())],
                        ],
                    ])),
                ],
            ],
        ];

        parent::__construct($config);
    }
}