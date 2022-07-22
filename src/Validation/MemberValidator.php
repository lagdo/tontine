<?php

namespace Siak\Tontine\Validation;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;

class MemberValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($values, [
            'gender' => 'required|in:F,M',
            'name' => 'required|string|min:1',
            'email' => 'sometimes|required|email',
            'phone' => 'sometimes|required|phone:AUTO',
            'birthday' => 'sometimes|required|date_format:Y-m-d',
            'city' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
