<?php

namespace GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeSetType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'AttributeSet',
            'description' => 'Represents a set of attributes',
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
                    'type' => Type::listOf(new AttributeItemType()),
                    'description' => 'The attributes in the set.',
                ],
                '__typename' => [
                    'type' => Type::string(),
                    'description' => 'The type name of the attribute set.',
                ],
            ],
        ];
        parent::__construct($config);
    }
}
