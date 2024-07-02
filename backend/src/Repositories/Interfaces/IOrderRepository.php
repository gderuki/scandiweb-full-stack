<?php

namespace Repositories\Interfaces;

use Repositories\Interfaces\IBaseRepository;

interface IOrderRepository extends IBaseRepository
{
    public function save(array $order): bool;
}
