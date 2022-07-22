<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\ValidationException;

class RemittanceValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($values, [
            'subscription' => 'required|integer|min:1',
            'amount' => 'required|integer|min:1',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
