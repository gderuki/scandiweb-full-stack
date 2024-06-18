<?php

namespace Services;

abstract class ValidatableService extends BaseService
{
/**
 * Validates the provided data.
 *
 * This method should be overridden by subclasses that require validation.
 * The default implementation throws an exception, indicating that validation is not implemented.
 *
 * @param mixed $data Data to be validated.
 * @throws RuntimeException If validation is not implemented.
 */
    public function validate(?array $data): bool
    {
        throw new RuntimeException("Validation not implemented for " . get_class($this));
    }
}
