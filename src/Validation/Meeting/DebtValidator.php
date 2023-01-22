<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\ValidationException;

use function explode;

class DebtValidator
{
    /**
     * @param string $debtId
     *
     * @return array
     */
    public function validate(string $debtId): array
    {
        $values = explode(':', $debtId);
        $values = [
            'type' => $values[0] ?? '',
            'loan_id' => $values[1] ?? 0,
        ];
        $validator = Validator::make($this->values($values), [
            'type' => 'required|in:p,i',
            'loan_id' => 'required|integer|min:1',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
