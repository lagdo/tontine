<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Exception;

use function implode;

class RemittanceValidator
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
