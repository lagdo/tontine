<?php

namespace Siak\Tontine\Validation;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Exception;

use function implode;

class ChargeValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($values, [
        ]);
        if($validator->fails())
        {
            throw new Exception(implode('<br/>', $validator->errors()->all()));
        }
        return $validator->validated();
    }
}
