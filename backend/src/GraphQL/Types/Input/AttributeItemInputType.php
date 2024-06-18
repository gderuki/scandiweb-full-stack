<?php

namespace GraphQL\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'AttributeInput',
            'description' => 'Input type for an attribute item',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The unique ID of the attribute item.',
                ],
            ],
        ];
        parent::__construct($config);
    }
}