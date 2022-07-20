<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Exception;

use function implode;

class SessionValidator extends AbstractValidator
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

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateVenue(array $values): array
    {
        $validator = Validator::make($values, [
            'title' => 'required|string|min:1',
            'date' => 'required|date_format:Y-m-d',
            'start' => 'required|date_format:H:I',
            'end' => 'required|date_format:H:I',
        ]);
        if($validator->fails())
        {
            throw new Exception(implode('<br/>', $validator->errors()->all()));
        }
        return $validator->validated();
    }
}
