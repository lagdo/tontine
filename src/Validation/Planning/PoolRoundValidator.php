<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\Planning\PoolRoundService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class PoolRoundValidator extends AbstractValidator
{
    /**
     * @param PoolRoundService $poolRoundService
     */
    public function __construct(private PoolRoundService $poolRoundService)
    {}

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'start_session' => 'required|integer|min:1',
            'end_session' => 'required|integer|min:1',
        ], [
            'start_session' => trans('tontine.pool.errors.start_session'),
            'end_session' => trans('tontine.pool.errors.end_session'),
        ]);
        $validator->after(function($validator) use($values) {
            // No more check if there's already an error.
            if($validator->errors()->count() > 0)
            {
                return;
            }
            $startSession = $this->poolRoundService->getSession((int)$values['start_session']);
            if(!$startSession)
            {
                $validator->errors()->add('start_session', trans('tontine.pool.errors.start_session'));
            }
            $endSession = $this->poolRoundService->getSession((int)$values['end_session']);
            if(!$endSession)
            {
                $validator->errors()->add('end_session', trans('tontine.pool.errors.end_session'));
            }
            if($endSession->id === $startSession->id || $endSession->start_at <= $startSession->start_at)
            {
                $validator->errors()->add('end_session', trans('tontine.pool.errors.session_dates'));
            }
        });
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return [
            'start_session_id' => (int)$values['start_session'],
            'end_session_id' => (int)$values['end_session'],
        ];
    }
}
