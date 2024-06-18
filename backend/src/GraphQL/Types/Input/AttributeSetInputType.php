<?php

namespace GraphQL\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class AttributeSetInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'AttributeSetInput',
            'description' => 'Input type for an attribute set',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The unique ID of the attribute set.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'The name of the attribute set.',
                ],
                'type' => [
                    'type' => Type::string(),
                    'description' => 'The type of the attribute set.',
                ],
                'items' => [
                    'type' => Type::listOf(new AttributeItemInputType()),
                    'description' => 'The attributes in the set.',
                ],
            ],
        ];
        parent::__construct($config);
    }
}