<?php

namespace Siak\Tontine\Validation\Tontine;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;
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
        $validator = Validator::make($this->values($values), [
            'type' => 'required|in:l,m,f',
            'name' => 'required|string|min:1',
            'shortname' => 'required|string|min:1',
            'biography' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|phone:AUTO',
            'city' => 'nullable|string|min:1',
            'address' => 'nullable|string',
            'website' => 'nullable|string',
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
