<?php

namespace Services;

use Repositories\Interfaces\IProductRepository;
use Services\Interfaces\IProductService;
use Psr\Log\LoggerInterface;

/**
 * Service class for managing product data.
 */
class ProductService extends ValidatableService implements IProductService
{
    public function __construct(IProductRepository $productRepository, LoggerInterface $logger)
    {
        parent::__construct($productRepository, $logger);
        $this->repository = $productRepository;

        $this->logger->info('Instance created', ['class' => get_class($this)]);
    }

    public function populate()
    {
        return $this->getAllProducts();
    }

    public function validate(?array $data): bool
    {
        if ($data === null) {
            $this->logger->error('Product data can\'t be null.');
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
