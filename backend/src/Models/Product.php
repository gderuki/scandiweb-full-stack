<?php

namespace Models;

use Models\BaseModel;
use Models\Attributes\AttributeSet;

class Product extends BaseModel
{
    public $id;
    public $name;
    public $inStock;
    public $description;
    public $brand;
    /**
     * @var AttributeSet[] An array of AttributeSet objects
     */
    public array $attributes = [];
    public $gallery = [];
    public $prices = [];
    public $category;
    public $category_id;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->inStock = (bool) $data['inStock'];
        $this->description = $data['description'];
        $this->category_id = $data['category_id'];
        $this->brand = $data['brand'];

        $this->__typename = 'Product';
    }

    public function appendAttributeSet(AttributeSet $attributeSet): void
    {
        $this->attributes[] = $attributeSet;
    }

    public function setGallery(array $galleryData): void
    {
        foreach ($galleryData as $url) {
            $this->gallery[] = $url;
        }
    }

    public function setPrices(array $pricesData): void
    {
        foreach ($pricesData as $priceData) {
            $this->prices[] = new PriceItem($priceData);
        }
    }

    protected function getArrayableProperties(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'inStock' => $this->inStock,
            'description' => $this->description,
            'brand' => $this->brand,
            'category' => $this->category,
            'attributes' => $this->attributes ?? [],
            'gallery' => $this->gallery,
            'prices' => array_map(function ($priceItem) {
                return $priceItem->asArray();
            }, $this->prices)
        ];
    }
}
