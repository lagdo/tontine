<?php

namespace Siak\Tontine\Validation;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Exception;

use function implode;

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
            'gender' => 'required|array:F,M',
            'name' => 'required|string|min:1',
            'email' => 'required|email',
            'phone' => 'phone:AUTO,US',
            'birthday' => 'sometimes|required|date_format:Y-m-d',
            'city' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
        ]);
        if($validator->fails())
        {
            throw new Exception(implode('<br/>', $validator->errors()->all()));
        }
        return $validator->validated();
    }
}
