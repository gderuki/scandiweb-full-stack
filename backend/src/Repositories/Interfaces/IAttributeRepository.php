<?php

namespace Repositories\Interfaces;

use Repositories\Interfaces\IBaseRepository;

/**
 * @marker for now
 */
interface IAttributeRepository extends IBaseRepository
{
    /**
     * Load all attributes for a product
     *
     * @param string $productId
     * @return Models\Attributes\AttributeSet[]
     */
    public function loadAttributes(string $productId): array;
}
