<?php

namespace Services;

use Services\BaseService;

/**
 * Service class for managing product data.
 */
class ProductService extends BaseService
{
    private $productRepository;

    public function __construct($productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function populate()
    {
        return $this->getAllProducts();
    }

    private function getAllProducts()
    {
        $products = $this->productRepository->getAll();
        $productArrays = array_map(function ($productModel) {
            return $productModel->asArray();
        }, $products);

        return $productArrays;
    }
}