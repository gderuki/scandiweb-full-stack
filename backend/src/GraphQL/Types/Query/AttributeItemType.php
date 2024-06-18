<?php

namespace GraphQL\Types\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType extends ObjectType {
    public function __construct()
    {
        $config = [
            'name' => 'Attribute',
            'description' => 'Represents an attribute',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The currency code (e.g., USD, EUR).',
                ],
                'value' => [
                    'type' => Type::string(),
                    'description' => 'The currency symbol (e.g., $, €).',
                ],
                'displayValue' => [
                    'type' => Type::string(),
                    'description' => 'The currency symbol (e.g., $, €).',
                ],
                '__typename' => [
                    'type' => Type::string(),
                    'description' => 'The type name of the currency.',
                ],
            ],
        ];
        parent::__construct($config);
    }
}