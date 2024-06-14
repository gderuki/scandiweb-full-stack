<?php

namespace Models;

use Models\Currency;

class PriceItem extends BaseModel
{
    public $amount;
    public Currency $currency;

    public function __construct($data)
    {
        $this->amount = $data['amount'];

        $this->__typename = 'Price';
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}