<?php

namespace Models;

/**
 * Abstract class BaseModel
 *
 * Serves as the base class for all model classes within the application.
 * Provides common functionality for converting model instances to arrays,
 * including handling of arrayable properties.
 */
abstract class BaseModel
{
    /**
     * @var string The type name of the model, used for identification.
     */
    public string $__typename;

    /**
     * Converts the model instance into an associative array.
     *
     * This method utilizes `getArrayableProperties` to determine which properties
     * should be included in the resulting array.
     * The `__typename` property is always included in the returned array.
     * WARNING: If a property is null it will be yielded as empty array.
     *
     * @return array An associative array representation of the model instance.
     */
    public function asArray()
    {
        $arrayableProperties = $this->getArrayableProperties();
        $array = [];

        foreach ($arrayableProperties as $property => $value) {
            if (property_exists($this, $property)) {
                if ((is_array($value) && empty($value))) {
                    $array[$property] = [];
                } else {
                    $array[$property] = $this->$property;
                }
            }
        }

        $array['__typename'] = $this->__typename;

        return $array;
    }

    /**
     * Retrieves an associative array of properties that should be included
     * when converting the model instance to an array.
     *
     * The default implementation returns an empty array, indicating that no
     * properties are arrayable by default. Subclasses should override this method
     * to specify which properties should be considered arrayable.
     *
     * @return array An associative array where keys are property names and values are default values.
     */
    protected function getArrayableProperties(): array
    {
        return [];
    }
}
