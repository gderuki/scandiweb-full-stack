<?php

namespace Repositories\Interfaces;

use Repositories\Interfaces\IBaseRepository;

interface IProductRepository extends IBaseRepository
{
    public function loadAttributes($productId);
}
