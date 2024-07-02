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
    public function allAttributesExist(array $triplets): bool
    {
        if (empty($triplets)) {
            return false;
        }

        $uniqueTriplets = array_unique($triplets, SORT_REGULAR);
        $values = [];
        $derivedTableRows = [];

        foreach ($uniqueTriplets as $index => $triplet) {
            $derivedTableRows[] = "SELECT ? AS product_id, ? AS attribute_id, ? AS value";
            $values[] = $triplet['productId'];
            $values[] = $triplet['attributeId'];
            $values[] = $triplet['value'];
        }

        $derivedTableQuery = implode(' UNION ALL ', $derivedTableRows);
        $query = <<<SQL
        SELECT COUNT(*) AS count
        FROM (
            $derivedTableQuery
        ) AS InputTriplets
        JOIN Attributes A ON A.id = InputTriplets.attribute_id
        JOIN AttributeItems AI ON AI.attribute_id = A.attribute_pk
            AND AI.product_id = InputTriplets.product_id
            AND AI.value = InputTriplets.value
        SQL;

        $stmt = $this->db->prepare($query);
        $stmt->execute($values);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] == count($uniqueTriplets);
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
