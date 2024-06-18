<?php

namespace GraphQL\Types\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CurrencyType extends ObjectType {
    public function __construct()
    {
        $config = [
            'name' => 'Currency',
            'description' => 'Represents a currency',
            'fields' => [
                'label' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The currency code (e.g., USD, EUR).',
                ],
                'symbol' => [
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