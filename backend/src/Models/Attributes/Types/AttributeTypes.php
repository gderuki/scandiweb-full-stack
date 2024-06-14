<?php

namespace Models\Attributes\Types;

/**
 * Enum representing the attribute types available in the system.
 */
enum AttributeTypes: string
{
    case CAPACITY = 'Capacity';
    case COLOR = 'Color';
    case SIZE = 'Size';
    case WITH_USB_3_PORTS = 'With USB 3 ports';
    case TOUCH_ID_IN_KEYBOARD = 'Touch ID in keyboard';

    public function toString(): string
    {
        return $this->value;
    }
}
