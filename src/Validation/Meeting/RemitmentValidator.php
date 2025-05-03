<?php

namespace Siak\Tontine\Validation\Meeting;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\Traits\ValidationTrait;
use Siak\Tontine\Validation\ValidationException;

class RemitmentValidator extends AbstractValidator
{
    use ValidationTrait;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(protected LocaleService $localeService)
    {}

    /**
     * @return array<string>
     */
    protected function amountFields(): array
    {
        return ['auction'];
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
            'auction' => $this->amountIfRule('remit_auction'),
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
