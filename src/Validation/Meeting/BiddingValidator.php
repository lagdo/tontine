<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Exception;

use function implode;

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
        ]);
        if($validator->fails())
        {
            throw new Exception(implode('<br/>', $validator->errors()->all()));
        }
        return $validator->validated();
    }
}
