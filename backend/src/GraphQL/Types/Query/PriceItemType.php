<?php

namespace GraphQL\Types\Query;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceItemType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'PriceItem',
            'description' => 'Represents a price item',
            'fields' => [
                'amount' => [
                    'type' => Type::float(),
                    'description' => 'The amount of the item.',
                ],
                'currency' => [
                    'type' => new CurrencyType(),
                    'description' => 'The currency of the price.',
                ],
                '__typename' => [
                    'type' => Type::string(),
                    'description' => 'The type name of the price item.',
                ],
            ],
        ];
        parent::__construct($config);
    }
}
