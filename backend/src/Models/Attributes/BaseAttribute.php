<?php

namespace Models\Attributes;

use Models\BaseModel;

/**
 * BaseAttribute is an abstract class that serves as the foundation for attribute models.
 * 
 * This class provides common properties and methods that are shared across different types
 * of attributes in the application.
 * 
 * @package Models\Attributes
 */
abstract class BaseAttribute extends BaseModel
{
    /**
     * The unique identifier for the attribute.
     * 
     * @var string
     */
    public string $id;

    /**
     * The value of the attribute.
     * 
     * @var string
     */
    public string $value;

    /**
     * The displayable representation of the attribute's value.
     * 
     * @var string
     */
    public string $displayValue;

    /**
     * Constructs a new instance of the BaseAttribute class.
     * 
     * Initializes the attribute with the provided data array which should
     * include keys for 'id', 'value', and 'displayValue'.
     * 
     * @param array $data The data to initialize the attribute with.
     */
    public function __construct(array $data)
    {
        if (!isset($data['id'], $data['value'], $data['displayValue'])) {
            throw new \InvalidArgumentException('Missing data for BaseAttribute initialization.');
        }

        $this->id = $data['id'];
        $this->value = $data['value'];
        $this->displayValue = $data['displayValue'];

        $this->__typename = 'Attribute';
    }
}
