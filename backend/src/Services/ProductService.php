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

    public function validate(?array $data): bool
    {
        if ($data === null) {
            return false;
        }

        $ids = [];
        foreach ($data as $product) {
            $ids[] = $product['productId'];
        }

        return $this->repository->allProductsExist($ids);
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
