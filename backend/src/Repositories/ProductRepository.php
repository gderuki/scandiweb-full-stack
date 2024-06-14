<?php

namespace Repositories;

use Utils\Database;
use Repositories\Interfaces\IProductRepository;
use Models\Product;
use Models\Factories\AttributeFactory;
use Models\Attributes\AttributeSet;
use Models\Attributes\Types\AttributeTypes;
use Models\PriceItem;
use Models\Currency;
use PDO;

class ProductRepository implements IProductRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function get($productId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM Products");
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[$row['id']] = new Product($row);
        }

        $this->loadCategories($products);
        $this->loadAttributes($products);
        $this->loadGallery($products);
        $this->loadPrices($products);
        $res = array_values($products);

        return $res;
    }

    private function loadCategories(&$products)
    {
        $categoryIds = array_map(function ($product) {
            return $product->category_id;
        }, $products);

        $categoryValues = array_values($categoryIds);

        $placeholders = implode(',', array_fill(0, count($categoryValues), '?'));

        $stmt = $this->db->prepare("SELECT * FROM Categories WHERE id IN ($placeholders)");
        $stmt->execute($categoryValues);

        $categoriesById = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoriesById[$row['id']] = $row;
        }

        foreach ($products as $product) {
            if (isset($categoriesById[$product->category_id])) {
                $product->category = $categoriesById[$product->category_id]['name'];
            }
        }
    }

    private function loadAttributes(&$products)
    {
        $productIds = array_keys($products);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM AttributeItems WHERE product_id IN ($placeholders)");
        $stmt->execute($productIds);

        $tempAttributeItems = [];
        $uniqueAttributeIds = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tempAttributeItems[$row['attribute_id']][] = $row;
            $uniqueAttributeIds[$row['attribute_id']] = true;
        }

        $attributeDetails = [];

        if (!empty($uniqueAttributeIds)) {
            $attributePks = array_keys($uniqueAttributeIds);
            $pkPlaceholders = implode(',', array_fill(0, count($attributePks), '?'));

            $idStmt = $this->db->prepare("SELECT id, attribute_pk FROM Attributes WHERE attribute_pk IN ($pkPlaceholders)");
            $idStmt->execute($attributePks);
            $idMap = [];
            while ($idRow = $idStmt->fetch(PDO::FETCH_ASSOC)) {
                $idMap[$idRow['attribute_pk']] = $idRow['id'];
            }

            $attributeIds = array_values($idMap);
            $attributePlaceholders = implode(',', array_fill(0, count($attributeIds), '?'));

            $attributeStmt = $this->db->prepare("SELECT * FROM Attributes WHERE id IN ($attributePlaceholders)");
            $attributeStmt->execute($attributeIds);
            while ($attributeRow = $attributeStmt->fetch(PDO::FETCH_ASSOC)) {
                $attributeDetails[$attributeRow['id']] = $attributeRow;
            }
        }

        foreach ($products as $product) {
            $productId = $product->id;

            foreach ($tempAttributeItems as $attributePk => $items) {
                $filteredItems = array_filter($items, function ($item) use ($productId) {
                    return $item['product_id'] == $productId;
                });

                if (isset($idMap[$attributePk])) {
                    $attributeId = $idMap[$attributePk];

                    if (!empty($filteredItems) && isset($attributeDetails[$attributeId])) {
                        $detail = $attributeDetails[$attributeId];
                        $attributeSet = new AttributeSet($detail);

                        $attributeItemsModels = array_map(function ($item) use ($attributeId) {
                            $item['attribute_id'] = AttributeTypes::tryFrom($attributeId); // dirty-dirty hack

                            return AttributeFactory::createAttributeItem($item);
                        }, $filteredItems);

                        $attributeSet->addItems(array_values($attributeItemsModels));
                        $product->appendAttributeSet($attributeSet);
                    }
                }
            }
        }
    }

    private function loadGallery(&$products)
    {
        $productIds = array_keys($products);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $stmt = $this->db->prepare("SELECT product_id, url FROM Gallery WHERE product_id IN ($placeholders)");
        $stmt->execute($productIds);

        foreach ($products as $product) {
            $product->setGallery([]);
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productID = $row['product_id'];
            $url = $row['url'];

            if (!in_array($url, $products[$productID]->gallery)) {
                $products[$productID]->gallery[] = $url;
            }
        }
    }

    private function loadPrices(&$products)
    {
        $productIds = implode(',', array_map(function ($id) {
            return $this->db->quote($id);
        }, array_keys($products)));

        $stmt = $this->db->prepare("SELECT p.id, p.product_id, p.amount, c.id AS currency_id, c.label, c.symbol
                                    FROM Prices p
                                    JOIN Currencies c ON p.currency_id = c.id
                                    WHERE p.product_id IN ($productIds)");
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $currency = new Currency($row);

            $priceItem = new PriceItem($row);
            $priceItem->setCurrency($currency);

            if (!isset($products[$row['product_id']]->prices)) {
                $products[$row['product_id']]->prices = [];
            }

            $products[$row['product_id']]->prices[] = $priceItem;
        }
    }
}
