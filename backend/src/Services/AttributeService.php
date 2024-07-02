<?php

namespace Services;

use Psr\Log\LoggerInterface;
use Repositories\Interfaces\IAttributeRepository;
use Services\Interfaces\IAttributeService;

class AttributeService extends ValidatableService implements IAttributeService
{
    public function __construct(IAttributeRepository $attributeRepository, LoggerInterface $logger)
    {
        parent::__construct($attributeRepository, $logger);
        $this->repository = $attributeRepository;

        $this->logger->info('Instance created', ['class' => get_class($this)]);
    }

    public function loadAttributesByProductId($productId)
    {
        return $this->repository->loadAttributes($productId);
    }

    public function validate(?array $data): bool
    {
        if ($data === null) {
            $this->logger->error('Attribute data can\'t be null.');
            return false;
        }

        $extractedData = [];

        foreach ($data as $product) {
            $productId = $product['productId'];

            if (!isset($product['attributes']) || empty($product['attributes'])) {
                return !$this->repository->productHasAnyAttributes($productId);
            }

            foreach ($product['attributes'] as $attribute) {
                if (is_numeric($attribute['key']) && $this->isJson($attribute['value'])) {
                    $decoded = json_decode($attribute['value'], true);
                    foreach ($decoded as $key => $value) {
                        $extractedData[] = [
                            'productId' => $productId,
                            'attributeId' => $key,
                            'value' => $value,
                        ];
                    }
                } else {
                    $extractedData[] = [
                        'productId' => $productId,
                        'attributeId' => $attribute['key'],
                        'value' => $attribute['value'],
                    ];
                }
            }
        }

        return $this->repository->allAttributesExist($extractedData);
    }
    private function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
