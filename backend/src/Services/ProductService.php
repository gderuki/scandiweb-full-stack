<?php

namespace Services;

use Repositories\Interfaces\IProductRepository;
use Services\BaseService;
use Services\Interfaces\IProductService;

/**
 * Service class for managing product data.
 */
class ProductService extends BaseService implements IProductService
{
    public function __construct(IProductRepository $productRepository)
    {
        $this->repository = $productRepository;
    }

    public function populate()
    {
        return $this->getAllProducts();
    }

    private function getAllProducts()
    {
        $products = $this->repository->getAll();
        $productArrays = array_map(function ($productModel) {
            return $productModel->asArray();
        }, $products);

        return $productArrays;
    }
}