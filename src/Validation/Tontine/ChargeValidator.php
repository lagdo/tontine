<?php

namespace Siak\Tontine\Validation\Tontine;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class ChargeValidator extends AbstractValidator
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'type' => 'required|integer|between:0,1',
            'period' => 'required|integer|between:0,3',
            'name' => 'required|string|min:1',
            'fixed' => [
                Rule::requiredIf((int)$values['type'] === 0 && (int)$values['period'] > 0),
                'in:1',
                'exclude',
            ],
            'amount' => 'required_if:fixed,1|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['amount'] = empty($values['fixed']) ? 0 :
            $this->localeService->convertMoneyToInt((float)$validated['amount']);
        $validated['lendable'] = isset($values['lendable']);

        return $validated;
    }
}
