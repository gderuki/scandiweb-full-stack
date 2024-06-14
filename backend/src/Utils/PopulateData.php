<?php

require_once '/app/vendor/autoload.php';

use Utils\Database;

function populateData()
{
    $pdo = Database::getInstance()->getConnection();

    // return if data exists
    $tablesToCheck = ['Categories', 'Products', 'Attributes', 'AttributeItems', 'Gallery', 'Prices', 'Currencies'];
    foreach ($tablesToCheck as $table) {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        if ($stmt->fetch()) {
            echo "Data already exists in the database. Exiting...\n";
            return;
        }
    }

    $jsonString = file_get_contents("/app/src/Utils/vendor-data.json");
    $data = json_decode($jsonString, true)['data'];

    // sanity check
    if (!isset($data['products']) || !is_array($data['products'])) {
        echo "Error: 'products' is not set or not an array.\n";
        exit;
    }

    foreach ($data['categories'] as $category) {
        $categoryStmt = $pdo->prepare("INSERT INTO Categories (name) VALUES (?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        $categoryStmt->execute([$category['name']]);
    }

    $categoryIds = [];
    $categoryStmt = $pdo->query("SELECT id, name FROM Categories");
    while ($row = $categoryStmt->fetch(PDO::FETCH_ASSOC)) {
        $categoryIds[$row['name']] = $row['id'];
    }

    foreach ($data['products'] as $product) {
        try {
            $pdo->beginTransaction();

            $categoryId = $categoryIds[$product['category']] ?? null;
            if ($categoryId === null) {
                throw new InvalidArgumentException("Category '{$product['category']}' not found.");
            }

            // product
            $productStmt = $pdo->prepare("INSERT INTO Products (id, name, inStock, description, category_id, brand) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), inStock = VALUES(inStock), category_id = VALUES(category_id)");
            $productStmt->execute([$product['id'], $product['name'], $product['inStock'], $product['description'], $categoryId, $product['brand']]);

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
                $attributeStmt = $pdo->prepare("INSERT INTO Attributes (id, name, type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type)");
                $attributeStmt->execute([$attribute['id'], $attribute['name'], $attribute['type']]);

                if ($attributeStmt->rowCount() > 0) {
                    $attributePk = $pdo->lastInsertId();
                } else {
                    $fetchStmt = $pdo->prepare("SELECT attribute_pk FROM Attributes WHERE id = ?");
                    $fetchStmt->execute([$attribute['id']]);
                    $attributePk = $fetchStmt->fetchColumn();
                }

                foreach ($attribute['items'] as $item) {
                    $attributeItemStmt = $pdo->prepare("INSERT INTO AttributeItems (attribute_id, product_id, id, value, displayValue) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value), displayValue = VALUES(displayValue)");
                    $attributeItemStmt->execute([$attributePk, $product['id'], $item['id'], $item['value'], $item['displayValue']]);
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
