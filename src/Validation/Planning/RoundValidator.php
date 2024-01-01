<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class RoundValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'title' => 'required|string|min:5',
            'notes' => 'nullable|string',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
