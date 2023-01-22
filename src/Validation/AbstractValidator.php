<?php

namespace Siak\Tontine\Validation;

use function array_map;
use function is_string;
use function trim;

abstract class AbstractValidator
{
    /**
     * Process input values before validation
     *
     * @param array $values
     *
     * @return array
     */
    protected function values(array $values): array
    {
        return array_map(function($value) {
            if(!is_string($value))
            {
                return $value;
            }
            // Replace empty values with null.
            $value = trim($value);
            return $value !== '' ? $value : null;
        }, $values);
    }

    /**
     * @param array $values
     *
     * @return array
     */
    abstract public function validateItem(array $values): array;

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateList(array $values): array
    {
        return array_map(function($value) {
            return $this->validateItem($value);
        }, $values);
    }
}
