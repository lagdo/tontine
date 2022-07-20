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
            'type' => 'required|array:0,1',
            'period' => 'required|array:0,1,2,3',
            'name' => 'required|string|min:1',
            'amount' => 'required|integer|min:1',
        ]);
        if($validator->fails())
        {
            throw new Exception(implode('<br/>', $validator->errors()->all()));
        }
        return $validator->validated();
    }
}
