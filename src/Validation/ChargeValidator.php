<?php

namespace Siak\Tontine\Validation;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Exception;

use function implode;

class ChargeValidator extends AbstractValidator
{
    public function validateItem(array $values)
    {
        $validator = Validator::make($values, [
        ]);
        if($validator->fails())
        {
            throw new Exception(implode('<br/>', $validator->errors()->all()));
        }
    }
}
