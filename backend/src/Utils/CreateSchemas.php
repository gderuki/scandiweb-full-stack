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

$productsTableQuery = "
CREATE TABLE Products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255),
    inStock BOOLEAN,
    description TEXT,
    category VARCHAR(255),
    brand VARCHAR(255)
);";
$galleryTableQuery = "
CREATE TABLE Gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    url VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);";
$attributeTableQuery = "
CREATE TABLE Attributes (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255),
    type VARCHAR(255)
);";
$attributeItemTableQuery = "
CREATE TABLE AttributeItems (
    id VARCHAR(255) PRIMARY KEY,
    attribute_id VARCHAR(255),
    product_id VARCHAR(255),
    displayValue VARCHAR(255),
    value VARCHAR(255),
    FOREIGN KEY (attribute_id) REFERENCES Attributes(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
);";
$currenciesTableQuery = "
CREATE TABLE Currencies (
    id VARCHAR(255) PRIMARY KEY,
    label VARCHAR(255),
    symbol VARCHAR(255)
);";
$pricesTableQuery = "
CREATE TABLE Prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    currency_id VARCHAR(255),
    amount DECIMAL(10, 2),
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (currency_id) REFERENCES Currencies(id)
);";

// Check and create tables
checkAndCreateTable($pdo, 'Products', $productsTableQuery);
checkAndCreateTable($pdo, 'Gallery', $galleryTableQuery);
checkAndCreateTable($pdo, 'Attributes', $attributeTableQuery);
checkAndCreateTable($pdo, 'AttributeItems', $attributeItemTableQuery);
checkAndCreateTable($pdo, 'Currencies', $currenciesTableQuery);
checkAndCreateTable($pdo, 'Prices', $pricesTableQuery);

