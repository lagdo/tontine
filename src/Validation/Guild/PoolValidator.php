<?php

namespace Siak\Tontine\Validation\Guild;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\Traits\ValidationTrait;
use Siak\Tontine\Validation\ValidationException;

use function trans;

class PoolValidator extends AbstractValidator
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
            'title' => 'required|string|min:5',
            'amount' => $this->amountRule(),
            'notes' => 'nullable|string',
            'properties' => 'required|array:deposit,remit',
            'properties.deposit' => 'required|array:fixed,lendable',
            'properties.deposit.fixed' => 'required|boolean',
            'properties.deposit.lendable' => 'required|boolean',
            'properties.remit' => 'required|array:planned,auction',
            'properties.remit.planned' => 'required|boolean',
            'properties.remit.auction' => 'required|boolean',
        ]);
        $validator->after(function($validator) use($values) {
            // The amount must be greater than 0 when the deposit property is set to fixed.
            if($values['properties']['deposit']['fixed'] && (float)$values['amount'] <= 0)
            {
                $validator->errors()
                    ->add('principal', trans('validation.gt.numeric', [
                        'attribute' => 'amount',
                        'value' => 0,
                    ]));
            }

            // Enforce rules on properties values.
            if(!$values['properties']['deposit']['fixed'])
            {
                $values['properties']['deposit']['lendable'] = false;
                $values['properties']['remit']['planned'] = true;
                $values['properties']['remit']['auction'] = false;
            }
        });
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['amount'] = $this->localeService->convertMoneyToInt((float)$validated['amount']);
        return $validated;
    }
}
