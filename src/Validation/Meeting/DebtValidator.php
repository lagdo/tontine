<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class DebtValidator extends AbstractValidator
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
     * Validate partial refund data
     *
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'debt' => 'required|integer|min:1',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['amount'] = $this->localeService->convertMoneyToInt((float)$validated['amount']);
        return $validated;
    }

    /**
     * @param string $debtId
     *
     * @return array
     */
    public function validate(string $debtId): array
    {
        $values = [
            'debt' => $debtId,
        ];
        $validator = Validator::make($this->values($values), [
            'debt' => 'required|integer|min:1',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
