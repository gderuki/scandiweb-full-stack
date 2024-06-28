<?php

namespace Repositories;

use Models\Attributes\AttributeSet;
use Models\Attributes\Types\AttributeTypes;
use Models\Currency;
use Models\Factories\AttributeFactory;
use Models\PriceItem;
use Models\Product;
use PDO;
use Repositories\Interfaces\IProductRepository;
use Utils\Database;

class ProductRepository implements IProductRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function get($productId): Product
    {
        $stmt = $this->db->prepare("SELECT * FROM Products WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$productData) {
            return null;
        }

        $product = new Product($productData);
        $products = [$product->id => $product];

        $this->loadCategories($products);
        $this->loadGallery($products);
        $this->loadPrices($products);

        return $products[$product->id];
    }

    public function get_old($productId): Product
    {
        $db = Database::getInstance()->getConnection();
        // basic info
        $stmt = $db->prepare("SELECT * FROM Products WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = new Product($stmt->fetch(PDO::FETCH_ASSOC));

        if (!$product) {
            return null;
        }

        // categories
        $stmt = $this->db->prepare("SELECT * FROM Categories WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // gallery
        $stmt = $db->prepare("SELECT * FROM Gallery WHERE product_id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product['gallery'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // prices
        $stmt = $db->prepare("SELECT * FROM Prices WHERE product_id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product['prices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // attributes
        $product['attributes'] = $this->loadAttributes($productId);

        return $product;
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
        $this->loadGallery($products);
        $this->loadPrices($products);

        return array_values($products);
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

    public function loadAttributes($productId)
    {
        $query = "SELECT ai.*, a.*
                    FROM AttributeItems ai
                    JOIN Attributes a
                    ON ai.attribute_id = a.attribute_pk
                    WHERE ai.product_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId]);

        $attributes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $attributes[$row['attribute_pk']][] = $row;
        }

        $attributeSets = [];
        foreach ($attributes as $attributeId => $items) {
            $attributeSet = new AttributeSet($items[0]); // first/any item has all the details
            $attributeItemsModels = array_map(function ($item) {
                $item['attribute_id'] = AttributeTypes::tryFrom($item['name']); // bad DB design
                return AttributeFactory::createAttributeItem($item);
            }, $items);
            $attributeSet->addItems($attributeItemsModels);
            $attributeSets[] = $attributeSet;
        }

        return $attributeSets;
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

    //region "VALIDATION"
    public function allAttributesExist(array $pairs): bool
    {
        if (empty($pairs)) {
            return false;
        }

        $placeholders = [];
        $values = [];
        foreach ($pairs as $pair) {
            $placeholders[] = "(product_id = ? AND id = ?)";
            $values[] = $pair['productId'];
            $values[] = $pair['attributeId'];
        }

        $placeholdersString = implode(' OR ', $placeholders);
        $query = "SELECT COUNT(*) AS count FROM AttributeItems WHERE " . $placeholdersString;

        $stmt = $this->db->prepare($query);
        $stmt->execute($values);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] == count($pairs);
    }

    public function productHasAnyAttributes($productId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM AttributeItems WHERE product_id = ?");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    public function allProductsExist(array $productIds): bool
    {
        $uniqueIds = array_unique($productIds);
        $numberOfIds = count($uniqueIds);

        if ($numberOfIds === 0) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, $numberOfIds, '?'));

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT id) FROM Products WHERE id IN ($placeholders)");
        $stmt->execute($uniqueIds);

        $foundIdsCount = $stmt->fetchColumn();

        return $foundIdsCount == $numberOfIds;
    }
    //endregion
}
