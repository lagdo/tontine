<?php

namespace Siak\Tontine\Validation;

abstract class AbstractValidator
{
    abstract public function validateItem(array $values);

    public function validateList(array $values)
    {
        foreach($values as $value)
        {
            $this->validateItem($value);
        }
    }
}
