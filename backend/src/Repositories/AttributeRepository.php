<?php

namespace Repositories;

use Models\Attributes\AttributeSet;
use Models\Attributes\Types\AttributeTypes;
use Models\Factories\AttributeFactory;
use PDO;
use Repositories\Interfaces\IAttributeRepository;
use Utils\Database;

class AttributeRepository implements IAttributeRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /*
     * Load all attributes for a product
     *
     * @return Models\Attributes\AttributeSet[]
     */
    public function loadAttributes(string $productId): array
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
    //endregion
}
