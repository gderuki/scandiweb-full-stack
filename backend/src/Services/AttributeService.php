<?php

namespace Services;

use Repositories\Interfaces\IProductRepository;
use Services\Interfaces\IAttributeService;

class AttributeService extends ValidatableService implements IAttributeService
{
    public function __construct(IProductRepository $productRepository)
    {
        $this->repository = $productRepository;
    }

    public function loadAttributesByProductId($productId)
    {
        return $this->repository->loadAttributes($productId);
    }

    public function validate(?array $data): bool
    {
        if ($data === null) {
            return false;
        }

        $extractedData = [];

        foreach ($data as $product) {
            $productId = $product['productId'];

            // handle null or empty attributes
            if (!isset($product['attributes']) || empty($product['attributes'])) {
                return !$this->repository->productHasAnyAttributes($productId);
            }

            foreach ($product['attributes'] as $attribute) {
                $attributeId = $attribute['id'];
                $extractedData[] = [
                    'productId' => $productId,
                    'attributeId' => $attributeId,
                ];
            }
        }

        return $this->repository->allAttributesExist($extractedData);
    }
}
