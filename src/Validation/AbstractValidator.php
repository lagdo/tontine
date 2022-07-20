<?php

namespace Siak\Tontine\Validation;

use function array_map;

abstract class AbstractValidator
{
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
