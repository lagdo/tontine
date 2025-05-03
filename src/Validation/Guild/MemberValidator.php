<?php

namespace Siak\Tontine\Validation\Guild;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

use function strtoupper;

class MemberValidator extends AbstractValidator
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
    {}

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $country = strtoupper($this->tenantService->guild()->country_code);
        $validator = Validator::make($this->values($values), [
            'name' => 'required|string|min:1',
            'email' => 'nullable|email',
            'phone' => 'nullable|phone:AUTO,' . $country,
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'registered_at' => 'nullable|date_format:Y-m-d',
            'birthday' => 'nullable|date_format:Y-m-d',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
