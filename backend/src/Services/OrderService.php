<?php

namespace Services;

use Psr\Log\LoggerInterface;
use Repositories\Interfaces\IOrderRepository;
use Services\Interfaces\IOrderService;
use Services\RepositoryService;

/**
 * Service class for managing category data.
 */
class OrderService extends RepositoryService implements IOrderService
{
    public function __construct(IOrderRepository $orderRepository, LoggerInterface $logger)
    {
        parent::__construct($orderRepository, $logger);
        $this->repository = $orderRepository;

        $this->logger->info('Instance created', ['class' => get_class($this)]);
    }
    
    public function saveOrder(array $order): bool
    {
        return $this->save($order);
    }

    private function save(array $order): bool
    {
        return $this->repository->save($order);
    }
}
