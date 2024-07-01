<?php

namespace Repositories\Interfaces;

use Repositories\Interfaces\IBaseRepository;

interface IProductRepository extends IDataRepository
{
    public function allProductsExist(array $productIds): bool;
}
