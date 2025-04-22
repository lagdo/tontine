<?php

namespace Siak\Tontine\Validation\Guild;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

use function substr;

class SessionValidator extends AbstractValidator
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Check duplicates in session date
     *
     * @param string $startAt
     * @param int $sessionId
     *
     * @return bool
     */
    private function sessionDateExists(string $startAt, int $sessionId): bool
    {
        return $this->tenantService->guild()->sessions()
            ->where('sessions.id', '!=', $sessionId)
            ->where('sessions.day_date', $startAt)
            ->first() !== null;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $values['start_time'] = substr($values['start_time'], 0, 5);
        $values['end_time'] = substr($values['end_time'], 0, 5);
        $validator = Validator::make($this->values($values), [
            'title' => 'required|string|min:1',
            'day_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'host_id' => 'integer|min:0',
        ]);
        $validator->after(function($validator) use($values) {
            if($this->sessionDateExists($values['day_date'], $values['id'] ?? 0))
            {
                $validator->errors()->add('day_date', trans('tontine.session.errors.date_dup'));
            }
        });
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        if(!$validated['host_id'])
        {
            $validated['host_id'] = null;
        }
        return $validated;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateVenue(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'venue' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        if($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
