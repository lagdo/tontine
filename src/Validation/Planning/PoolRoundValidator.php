<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as RealValidator;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class PoolRoundValidator extends AbstractValidator
{
    /**
     * @var array
     */
    private array $values = [];

    /**
     * @param SessionService $sessionService
     */
    public function __construct(private SessionService $sessionService)
    {}

    /**
     * @param array $values
     * @param string $key
     * @param RealValidator $validator
     *
     * @return void
     */
    private function getSession(array $values, string $key, RealValidator $validator): void
    {
        if(!isset($values[$key]))
        {
            return;
        }
        $this->values["{$key}_id"] = (int)$values[$key];
        $session = $this->sessionService->getTontineSession($this->values["{$key}_id"]);
        if($session !== null)
        {
            $this->values[$key] = $session;
            return;
        }
        $validator->errors()->add($key, trans("tontine.pool_round.errors.$key"));
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'start_session' => 'required_without:end_session|integer|min:1',
            'end_session' => 'required_without:start_session|integer|min:1',
        ], [
            'start_session' => trans('tontine.pool_round.errors.start_session'),
            'end_session' => trans('tontine.pool_round.errors.end_session'),
        ]);
        $this->values = [];
        $validator->after(function($validator) use($values) {
            // No more check if there's already an error.
            if($validator->errors()->count() > 0)
            {
                return;
            }

            $this->getSession($values, 'start_session', $validator);
            $this->getSession($values, 'end_session', $validator);
            // Todo: check that the start session comes before the end session.
            // if($endSession->id === $startSession->id || $endSession->start_at <= $startSession->start_at)
            // {
            //     $validator->errors()->add('end_session', trans('tontine.pool_round.errors.session_dates'));
            // }
        });

        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return $this->values;
    }
}
