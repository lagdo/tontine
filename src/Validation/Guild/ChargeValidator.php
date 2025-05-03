<?php

namespace Siak\Tontine\Validation\Guild;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\Traits\ValidationTrait;
use Siak\Tontine\Validation\ValidationException;

class ChargeValidator extends AbstractValidator
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
            'type' => 'required|integer|between:0,1',
            'period' => 'required|integer|between:0,3',
            'name' => 'required|string|min:1',
            'fixed' => [
                Rule::requiredIf((int)$values['type'] === 0 && (int)$values['period'] > 0),
                'in:1',
                'exclude',
            ],
            'amount' => $this->amountIfRule('fixed'),
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
