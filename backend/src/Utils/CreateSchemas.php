<?php

require '/app/vendor/autoload.php';

use Utils\Database;

try {
    $pdo = Database::getInstance()->getConnection();
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

function logMessage($level, $message)
{
    $timestamp = date('Y-m-d H:i:s');
    echo "{$timestamp} [{$level}] {$message}\n";
}

function checkAndCreateTable($pdo, $tableName, $createQuery)
{
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() == 0) {
            if ($pdo->exec($createQuery) === false) {
                $errorInfo = $pdo->errorInfo();
                logMessage("ERROR", "Error creating table '$tableName': " . $errorInfo[2]);
            } else {
                logMessage("INFO", "Table '$tableName' created successfully.");
            }
        } else {
            logMessage("WARN", "Table '$tableName' already exists.");
        }
    } catch (\PDOException $e) {
        logMessage("ERROR", "PDOException when creating table '$tableName': " . $e->getMessage());
    }
}

$categoriesTableQuery = "
CREATE TABLE Categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL
) COLLATE='utf8_general_ci';";

$productsTableQuery = "
CREATE TABLE Products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255),
    inStock BOOLEAN,
    description TEXT,
    category_id INT,
    brand VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES Categories(id)
) COLLATE='utf8_general_ci';";

$galleryTableQuery = "
CREATE TABLE Gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    url VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES Products(id)
) COLLATE='utf8_general_ci';";

$attributeTableQuery = "
CREATE TABLE Attributes (
    attribute_pk INT AUTO_INCREMENT PRIMARY KEY,
    id VARCHAR(255),
    name VARCHAR(255),
    type VARCHAR(255),
    UNIQUE(id, name, type)
) COLLATE='utf8_general_ci';";

$attributeItemTableQuery = "
CREATE TABLE AttributeItems (
    item_pk INT AUTO_INCREMENT PRIMARY KEY,
    attribute_id INT,
    product_id VARCHAR(255),
    id VARCHAR(255),
    displayValue VARCHAR(255),
    value VARCHAR(255),
    FOREIGN KEY (attribute_id) REFERENCES Attributes(attribute_pk),
    FOREIGN KEY (product_id) REFERENCES Products(id)
) COLLATE='utf8_general_ci';";

$currenciesTableQuery = "
CREATE TABLE Currencies (
    id VARCHAR(255) PRIMARY KEY,
    label VARCHAR(255),
    symbol VARCHAR(255)
) COLLATE='utf8_general_ci';";

$pricesTableQuery = "
CREATE TABLE Prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    currency_id VARCHAR(255),
    amount DECIMAL(10, 2),
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (currency_id) REFERENCES Currencies(id)
) COLLATE='utf8_general_ci';";

$orderTableQuery = "
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) COLLATE='utf8_general_ci';";

$orderProductsTableQuery = "
CREATE TABLE OrderProducts (
    order_product_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id VARCHAR(255),
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id)
) COLLATE='utf8_general_ci';";

$productAttributesTableQuery = "
CREATE TABLE ProductAttributes (
    attribute_id INT AUTO_INCREMENT PRIMARY KEY,
    order_product_id INT,
    attribute_name VARCHAR(255),
    attribute_value VARCHAR(255),
    FOREIGN KEY (order_product_id) REFERENCES OrderProducts(order_product_id)
) COLLATE='utf8_general_ci';";

// Check and create tables
checkAndCreateTable($pdo, 'Categories', $categoriesTableQuery);
checkAndCreateTable($pdo, 'Products', $productsTableQuery);
checkAndCreateTable($pdo, 'Gallery', $galleryTableQuery);
checkAndCreateTable($pdo, 'Attributes', $attributeTableQuery);
checkAndCreateTable($pdo, 'AttributeItems', $attributeItemTableQuery);
checkAndCreateTable($pdo, 'Currencies', $currenciesTableQuery);
checkAndCreateTable($pdo, 'Prices', $pricesTableQuery);
checkAndCreateTable($pdo, 'Orders', $orderTableQuery);
checkAndCreateTable($pdo, 'OrderProducts', $orderProductsTableQuery);
checkAndCreateTable($pdo, 'ProductAttributes', $productAttributesTableQuery);