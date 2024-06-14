<?php

namespace Models\Attributes;

use Models\BaseModel;

class AttributeSet extends BaseModel
{
    public $id;
    public $name;
    public $type;
    public $items = [];

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->type = $data['type'];

        $this->__typename = 'AttributeSet';
    }

    public function addItem($item): void
    {
        $this->items[] = $item;
    }

    public function addItems($items): void
    {
        $this->items = $items;
    }
}
