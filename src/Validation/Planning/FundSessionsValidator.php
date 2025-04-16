<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as RealValidator;
use Siak\Tontine\Service\Planning\FundService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

use function array_map;
use function Jaxon\jaxon;

class FundSessionsValidator extends AbstractValidator
{
    /**
     * @param FundService $fundService
     */
    public function __construct(private FundService $fundService)
    {}

    /**
     * @param RealValidator $validator
     * @param array $errors
     *
     * @return void
     */
    private function validateDates(RealValidator $validator, array $errors): void
    {
        $stash = jaxon()->di()->getStash();
        $fund = $stash->get('planning.finance.fund');
        $sessions = [
            'start_sid' => $fund->start,
            'end_sid' => $fund->end,
            'interest_sid' => $fund->interest,
        ];

        $values = $validator->validated();
        $allSessionsFound = true;
        foreach($errors as $key => $message)
        {
            if(!isset($values[$key]))
            {
                continue;
            }
            $sessions[$key] = $this->fundService->getGuildSession((int)$values[$key]);
            if(!$sessions[$key])
            {
                $allSessionsFound = false;
                $validator->errors()->add($key, $message);
            }
        }
        if(!$allSessionsFound)
        {
            return;
        }

        // Verify that the start session comes before the end session.
        if($sessions['end_sid']->start_at <= $sessions['start_sid']->start_at)
        {
            $validator->errors()->add('end_sid', trans('tontine.session.errors.dates.end'));
        }
        // Verify that the interest session is between the start and end sessions.
        if($sessions['interest_sid']->start_at <= $sessions['start_sid']->start_at ||
            $sessions['interest_sid']->start_at > $sessions['end_sid']->start_at)
        {
            $validator->errors()->add('interest_sid', trans('tontine.session.errors.dates.int'));
        }
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $rules = [
            'start_sid' => 'required_without_all:end_sid,interest_sid|integer|min:1',
            'end_sid' => 'required_without_all:start_sid,interest_sid|integer|min:1',
            'interest_sid' => 'required_without_all:start_sid,end_sid|integer|min:1',
        ];
        $errors = [
            'start_sid' => trans('tontine.session.errors.start'),
            'end_sid' => trans('tontine.session.errors.end'),
            'interest_sid' => trans('tontine.session.errors.interest'),
        ];
        $validator = Validator::make($this->values($values), $rules, $errors);
        $validator->after(function($validator) use($errors) {
            // No more check if there's already an error.
            if($validator->errors()->count() === 0)
            {
                $this->validateDates($validator, $errors);
            }
        });

        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return array_map(fn($sid) => (int)$sid, $validator->validated());
    }
}
