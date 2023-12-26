<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class RemitmentValidator extends AbstractValidator
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
            'payable' => 'required|integer|min:1',
            'auction' => 'required_if:remit_auction,1|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['auction'] = empty($validated['auction']) ? 0 :
            $this->localeService->convertMoneyToInt((float)$validated['auction']);
        return $validated;
    }
}
