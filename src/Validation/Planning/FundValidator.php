<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class FundValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($values, [
            'title' => 'required|string|min:1',
            'amount' => 'required|integer|min:1',
            'notes' => 'present|string',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
