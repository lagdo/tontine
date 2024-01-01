<?php

namespace Siak\Tontine\Validation\Planning;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

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
        return $this->tenantService->tontine()->sessions()
            ->where('sessions.id', '!=', $sessionId)
            ->whereDate('sessions.start_at', $startAt)
            ->first() !== null;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'title' => 'required|string|min:1',
            'date' => 'required|date_format:Y-m-d',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'host_id' => 'integer|min:0',
        ]);
        $validator->after(function($validator) use($values) {
            if($this->sessionDateExists($values['date'], $values['id'] ?? 0))
            {
                $validator->errors()->add('date', trans('tontine.session.errors.date_dup'));
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
