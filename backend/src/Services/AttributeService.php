<?php

namespace Services;

use Repositories\AttributeRepository;
use Repositories\Interfaces\IProductRepository;
use Services\Interfaces\IAttributeService;

class AttributeService extends BaseService implements IAttributeService
{
    public function __construct(IProductRepository $productRepository)
    {
        $this->repository = $productRepository;
    }

    public function loadAttributesByProductId($productId)
    {
        return $this->repository->loadAttributes($productId);
    }
}
