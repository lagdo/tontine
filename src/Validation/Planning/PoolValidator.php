<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class PoolValidator extends AbstractValidator
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
            'title' => 'required|string|min:1',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'notes' => 'nullable|string',
            'properties' => 'required|array:deposit,remit',
            'properties.deposit' => 'required|array:fixed,lendable',
            'properties.deposit.fixed' => 'required|boolean',
            'properties.deposit.lendable' => 'required|boolean',
            'properties.remit' => 'required|array:planned,auction',
            'properties.remit.planned' => 'required|boolean',
            'properties.remit.auction' => 'required|boolean',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['amount'] = $this->localeService->convertMoneyToInt((float)$validated['amount']);
        return $validated;
    }
}
