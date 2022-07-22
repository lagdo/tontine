<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\ValidationException;

class BiddingValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($values, [
            'member' => 'required|integer|min:1',
            'amount_bid' => 'required|integer|min:1',
            'amount_paid' => 'required|integer|min:0',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
