<?php

namespace Siak\Tontine\Validation\Tontine;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\AbstractValidator;

use function strtoupper;

class MemberValidator extends AbstractValidator
{
    /**
     * @var TenantService
     */
    private $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $country = strtoupper($this->tenantService->tontine()->country->code);
        $validator = Validator::make($values, [
            'name' => 'required|string|min:1',
            'email' => 'sometimes|required|email',
            'phone' => 'sometimes|required|phone:AUTO,' . $country,
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
