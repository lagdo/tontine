<?php

namespace Siak\Tontine\Validation\Traits;

use function str_replace;

trait ValidationTrait
{
    /**
     * @return array<string>
     */
    abstract protected function amountFields(): array;

    /**
     * Process input values before validation
     *
     * @param array $values
     *
     * @return array
     */
    protected function values(array $values): array
    {
        $values = parent::values($values);
        $fields = $this->amountFields();
        foreach($fields as $field)
        {
            if(isset($values[$field]))
            {
                $values[$field] = str_replace(',', '.', $values[$field]);
            }
        }
        return $values;
    }

    /**
     * @return string
     */
    protected function amountRule(): string
    {
        return 'required|regex:/^\d+([\.\,]\d{1,2})?$/';
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected function amountIfRule(string $field): string
    {
        return "required_if:{$field},1|regex:/^\d+([\.\,]\d{1,2})?$/";
    }
}
