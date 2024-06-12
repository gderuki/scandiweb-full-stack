<?php

require_once '/app/vendor/autoload.php';

use Utils\Database;

function populateData()
{
    $pdo = Database::getInstance()->getConnection();
    $jsonString = file_get_contents("/app/src/Utils/vendor-data.json");
    $data = json_decode($jsonString, true)['data'];

    // sanity check
    if (!isset($data['products']) || !is_array($data['products'])) {
        echo "Error: 'products' is not set or not an array.\n";
        exit;
    }

    foreach ($data['products'] as $product) {
        try {
            $pdo->beginTransaction();

            // product
            $productStmt = $pdo->prepare("INSERT INTO Products (id, name, inStock, description, category, brand) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), inStock = VALUES(inStock)");
            $productStmt->execute([$product['id'], $product['name'], $product['inStock'], $product['description'], $product['category'], $product['brand']]);

            // gallery
            foreach ($product['gallery'] as $url) {
                $galleryStmt = $pdo->prepare("INSERT INTO Gallery (product_id, url) VALUES (?, ?) ON DUPLICATE KEY UPDATE url = VALUES(url)");
                $galleryStmt->execute([$product['id'], $url]);
            }

            // prices
            foreach ($product['prices'] as $price) {
                $currencyLabel = strtolower($price['currency']['label']);
                $stmt = $pdo->prepare("INSERT IGNORE INTO Currencies (id, label, symbol) VALUES (?, ?, ?)");
                $stmt->execute([$currencyLabel, $price['currency']['label'], $price['currency']['symbol']]);

                $priceStmt = $pdo->prepare("INSERT INTO Prices (product_id, amount, currency_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = VALUES(amount)");
                $priceStmt->execute([$product['id'], $price['amount'], $currencyLabel]);
            }

            // AttributeSets and Attributes, in my database I call them Attributes and AttributeItems
            foreach ($product['attributes'] as $attribute) {
                $attributeStmt = $pdo->prepare("INSERT INTO Attributes (id, name, type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
                $attributeStmt->execute([$attribute['id'], $attribute['name'], $attribute['type']]);

                // attribute items
                foreach ($attribute['items'] as $item) {
                    $attributeItemStmt = $pdo->prepare("INSERT INTO AttributeItems (attribute_id, product_id, id, value, displayValue) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value), displayValue = VALUES(displayValue)");
                    $attributeItemStmt->execute([$attribute['id'], $product['id'], $item['id'], $item['value'], $item['displayValue']]);
                }
            }

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "General error: " . $e->getMessage();
        }
    }
}

populateData();