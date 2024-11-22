<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class PoolRoundValidator extends AbstractValidator
{
    /**
     * @var string
     */
    private string $key = 'start_session';

    /**
     * @param SessionService $sessionService
     */
    public function __construct(private SessionService $sessionService)
    {}

    /**
     * @return static
     */
    public function start(): static
    {
        $this->key = 'start_session';

        return $this;
    }

    /**
     * @return static
     */
    public function end(): static
    {
        $this->key = 'end_session';

        return $this;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            $this->key => 'required|integer|min:1',
        ], [
            $this->key => trans('tontine.pool_round.errors.' . $this->key),
        ]);
        $validator->after(function($validator) use($values) {
            // No more check if there's already an error.
            if($validator->errors()->count() > 0)
            {
                return;
            }

            $session = $this->sessionService->getTontineSession((int)$values[$this->key]);
            if(!$session)
            {
                $validator->errors()->add($this->key, trans('tontine.pool_round.errors.' . $this->key));
            }
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

        return [
            $this->key . '_id' => (int)$values[$this->key],
        ];
    }
}
