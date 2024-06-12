<?php

require '/app/vendor/autoload.php';

use Utils\Database;

$pdo = Database::getInstance()->getConnection();

$productsQuery = "SELECT * FROM Products";
$productsStmt = $pdo->query($productsQuery);
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as &$product) {
    $product['inStock'] = $product['inStock'] ? true : false;

    // gallery
    $galleryStmt = $pdo->prepare("SELECT * FROM Gallery WHERE product_id = ?");
    $galleryStmt->execute([$product['id']]);
    $gallery = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);
    $product['gallery'] = array_map(function($image) {
        return $image['url']; // only url returned
    }, $gallery);

    // attributes
    $attributesStmt = $pdo->prepare("SELECT * FROM Attributes");
    $attributes = $attributesStmt->execute() ? $attributesStmt->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($attributes as &$attribute) {
        // attribute items
        $attributeItemsStmt = $pdo->prepare("SELECT * FROM AttributeItems WHERE attribute_id = ? AND product_id = ?");
        $attributeItemsStmt->execute([$attribute['id'], $product['id']]);
        $attributeItems = $attributeItemsStmt->fetchAll(PDO::FETCH_ASSOC);
        $attributeItems = array_map(function($item) {
            return [
                'id' => $item['id'],
                'value' => $item['value'],
                'displayValue' => $item['displayValue'],
                '__typename' => 'Attribute',
            ];
        }, $attributeItems);
        $attribute['items'] = $attributeItems;
        $attribute['__typename'] = 'AttributeSet';
    }

    $attributes = array_filter($attributes, function($attribute) {
        return !empty($attribute['items']);
    });

    $product['attributes'] = $attributes;

    $product['__typename'] = 'Product';

    // prices
    $pricesStmt = $pdo->prepare("SELECT * FROM Prices WHERE product_id = ?");
    $pricesStmt->execute([$product['id']]);
    $prices = $pricesStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($prices as &$price) {
        $price['__typename'] = 'Price';
    }
    $product['prices'] = $prices;
}

$response = [
    'products' => $products,
    'categories' => []
];

header('Content-Type: application/json');
echo json_encode($response);