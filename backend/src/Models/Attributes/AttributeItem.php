<?php

namespace Models\Attributes;

use Models\BaseModel;

class AttributeItem extends BaseModel
{
    public $id;
    public $value;
    public $displayValue;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->value = $data['value'];
        $this->displayValue = $data['displayValue'];

        $this->__typename = 'Attribute';
    }
}