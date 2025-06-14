<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as RealValidator;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

use function jaxon;

class PoolSessionsValidator extends AbstractValidator
{
    /**
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
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
        $guild = $stash->get('tenant.guild');
        $pool = $stash->get('planning.pool');

        $sessions = [
            'start_sid' => $pool->start,
            'end_sid' => $pool->end,
        ];

        $values = $validator->validated();
        $allSessionsFound = true;
        foreach($errors as $key => $message)
        {
            if(!isset($values[$key]))
            {
                continue;
            }
            $sessions[$key] = $this->poolService
                ->getGuildSession($guild, (int)$values[$key]);
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
        if($sessions['end_sid']->day_date <= $sessions['start_sid']->day_date)
        {
            $validator->errors()->add('end_sid', trans('tontine.session.errors.dates.end'));
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
            'start_sid' => 'required_without:end_sid|integer|min:1',
            'end_sid' => 'required_without:start_sid|integer|min:1',
        ];
        $errors = [
            'start_sid' => trans('tontine.session.errors.start'),
            'end_sid' => trans('tontine.session.errors.end'),
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
