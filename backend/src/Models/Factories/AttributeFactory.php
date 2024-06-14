<?php

namespace Models\Factories;

use Models\Attributes\Types\AttributeTypes;
use Models\Attributes\Types\CapacityAttribute;
use Models\Attributes\Types\ColorAttribute;
use Models\Attributes\Types\SizeAttribute;
use Models\Attributes\Types\YesNoAttribute;

/**
 * AttributeFactory is responsible for creating attribute objects based on the attribute type.
 * 
 * This factory uses a mapping of attribute types to their corresponding class names to instantiate
 * the appropriate attribute object. It supports a variety of attribute types, including capacity,
 * size, color, and boolean attributes like USB 3 ports availability or Touch ID in keyboard.
 */
class AttributeFactory
{
    /**
     * Returns the type map for attribute types to their corresponding class names.
     * 
     * @return array The type map array.
     */
    private static function getTypeMap(): array
    {
        return [
            AttributeTypes::CAPACITY->value => CapacityAttribute::class,
            AttributeTypes::SIZE->value => SizeAttribute::class,
            AttributeTypes::COLOR->value => ColorAttribute::class,
            AttributeTypes::WITH_USB_3_PORTS->value => YesNoAttribute::class,
            AttributeTypes::TOUCH_ID_IN_KEYBOARD->value => YesNoAttribute::class,
        ];
    }

    /**
     * Creates an attribute object based on the provided details.
     * 
     * @param array $detail The details of the attribute to create. 
     *                      $detail['attribute_id'] must be of type AttributeTypes.
     * @return object An instance of the attribute class corresponding to the provided attribute type.
     * @throws \InvalidArgumentException If the attribute type is invalid or not recognized.
     */
    public static function createAttributeItem(array $detail)
    {
        if (!isset($detail['attribute_id']) || !$detail['attribute_id'] instanceof AttributeTypes) {
            throw new \InvalidArgumentException("Attribute type is missing or invalid!");
        }

        $attributeType = $detail['attribute_id'];

        $typeMap = self::getTypeMap();

        if (!isset($typeMap[$attributeType->value])) {
            throw new \InvalidArgumentException("Invalid attribute type! `{$attributeType->value}` doesn't exist!");
        }

        $className = $typeMap[$attributeType->value];

        return new $className($detail);
    }
}
