<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\Traits\ValidationTrait;
use Siak\Tontine\Validation\ValidationException;

use function trans;

class LoanValidator extends AbstractValidator
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
        return ['principal', 'interest'];
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'member' => 'required|integer|min:1',
            'fund' => 'required|integer|min:1',
            'principal' => $this->amountRule(),
            'interest_type' => 'required|in:f,u,s,c',
            'interest' => $this->amountRule(),
        ]);
        $validator->after(function($validator) use($values) {
            if((float)$values['principal'] <= 0)
            {
                $validator->errors()->add('principal', trans('validation.gt.numeric', [
                    'attribute' => trans('meeting.loan.labels.principal'),
                    'value' => 0,
                ]));
            }
        });
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['principal'] = $this->localeService
            ->convertMoneyToInt((float)$validated['principal']);
        // Interest rates must be saved as int, so the value is multiplied by 100.
        $validated['interest_rate'] = $validated['interest_type'] === 'f' ? 0 :
            (int)(100 * $validated['interest']);
        $validated['interest'] = $validated['interest_type'] === 'f' ?
            $this->localeService->convertMoneyToInt((float)$validated['interest']) :
            (int)($validated['principal'] * ((float)$validated['interest'] / 100));

        return $validated;
    }
}
