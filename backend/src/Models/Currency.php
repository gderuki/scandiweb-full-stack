<?php

namespace Models;

class Currency extends BaseModel
{
    public $label;
    public $symbol;

    public function __construct($data)
    {
        $this->label = $data['label'];
        $this->symbol = $data['symbol'];

        $this->__typename = 'Currency';
    }
}