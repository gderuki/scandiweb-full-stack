<?php

namespace Services;

use Psr\Log\LoggerInterface;
use Repositories\Interfaces\IProductRepository;
use Services\Interfaces\IProductService;

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

    public function getAll()
    {
        return $this->getAllProducts();
    }

    public function get($id)
    {
        return $this->getProductById($id);
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

    private function getProductById($productId)
    {
        $product = $this->repository->get($productId);
        return $product->asArray();
    }
}
