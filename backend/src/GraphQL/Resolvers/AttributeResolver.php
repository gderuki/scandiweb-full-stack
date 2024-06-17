<?php

namespace GraphQL\Resolvers;

use GraphQL\Resolvers\Interfaces\IAttributeResolver;
use Services\Interfaces\IAttributeService;

class AttributeResolver implements IAttributeResolver
{
    protected $serviceLocator;
    protected $attributeService;

    public function __construct(\ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->initializeServices();
    }

    protected function initializeServices()
    {
        $this->attributeService = $this->serviceLocator->get(IAttributeService::class);
    }

    public function resolveAttributes($productId)
    {
        return $this->attributeService->loadAttributesByProductId($productId);
    }
}
