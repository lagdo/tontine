<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\Traits\ValidationTrait;
use Siak\Tontine\Validation\ValidationException;

class OutflowValidator extends AbstractValidator
{
    use ValidationTrait;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(private LocaleService $localeService)
    {}

    /**
     * @return array<string>
     */
    protected function amountFields(): array
    {
        return ['amount'];
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'member' => 'required|integer|min:0',
            'charge' => 'required|integer|min:0',
            'category' => 'required|integer|min:1',
            'amount' => $this->amountRule(),
            'comment' => 'present|between:0,150',
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
