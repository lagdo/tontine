<?php

namespace Siak\Tontine\Validation\Tontine;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\AbstractValidator;
use Spatie\ValidationRules\Rules\CountryCode;
use Spatie\ValidationRules\Rules\Currency as CurrencyCode;

use function strtoupper;

class TontineValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($values, [
            'type' => 'required|in:m,f',
            'name' => 'required|string|min:1',
            'shortname' => 'required|string|min:1',
            // 'biography' => 'sometimes|required|string',
            // 'email' => 'sometimes|required|email',
            // 'phone' => 'sometimes|required|phone:AUTO',
            'city' => 'sometimes|required|string|min:1',
            // 'address' => 'sometimes|required|string',
            // 'website' => 'sometimes|required|string',
            'country_code' => ['required', new CountryCode()],
            'currency_code' => ['required', new CurrencyCode()],
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
