<?php

namespace Repositories;

use Psr\Log\LoggerInterface;
use Repositories\Interfaces\IOrderRepository;
use Utils\Database;

class OrderRepository implements IOrderRepository
{
    private $db;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(array $order): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("INSERT INTO Orders (created_at) VALUES (NOW())");
            $stmt->execute();
            $orderId = $this->db->lastInsertId();

            foreach ($order as $product) {
                $stmt = $this->db->prepare("INSERT INTO OrderProducts (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$orderId, $product['productId'], $product['quantity']]);
                $orderProductId = $this->db->lastInsertId();

                foreach ($product['attributes'] as $attribute) {
                    if (is_numeric($attribute['key']) && is_string($attribute['value']) && is_array(json_decode($attribute['value'], true))) {
                        $decodedAttributes = json_decode($attribute['value'], true);
                        foreach ($decodedAttributes as $attrName => $attrValue) {
                            $stmt = $this->db->prepare("INSERT INTO ProductAttributes (order_product_id, attribute_name, attribute_value) VALUES (?, ?, ?)");
                            $stmt->execute([$orderProductId, $attrName, $attrValue]);
                        }
                    } else {
                        $stmt = $this->db->prepare("INSERT INTO ProductAttributes (order_product_id, attribute_name, attribute_value) VALUES (?, ?, ?)");
                        $stmt->execute([$orderProductId, $attribute['key'], $attribute['value']]);
                    }
                }
            }
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            $logger->error('Something went wrong while saving the order: ' . $e->getMessage());
            return false;
        }
    }
}
