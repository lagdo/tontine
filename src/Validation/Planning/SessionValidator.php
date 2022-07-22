<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

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
            'title' => 'required|string|min:1',
            'date' => 'required|date_format:Y-m-d',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'host_id' => 'sometimes|required|integer|min:0',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
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
            'venue' => 'present|string',
            'notes' => 'present|string',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
