<?php

namespace Services\Interfaces;

interface IOrderService 
{
    /**
     * Saves an order.
     *
     * @param array $order The order to save.
     * @return bool True if the order was saved successfully, false otherwise.
     */
    public function saveOrder(array $order): bool;
}
