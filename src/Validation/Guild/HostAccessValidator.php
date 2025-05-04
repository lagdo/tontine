<?php

namespace Siak\Tontine\Validation\Guild;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class HostAccessValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'admin' => 'sometimes|required|array',
            'admin.guilds' => 'sometimes|required|in:1',
            'finance' => 'sometimes|required|array',
            'finance.charges' => 'sometimes|required|in:1',
            'finance.accounts' => 'sometimes|required|in:1',
            'finance.pools' => 'sometimes|required|in:1',
            'tontine' => 'sometimes|required|array',
            'tontine.members' => 'sometimes|required|in:1',
            'tontine.calendar' => 'sometimes|required|in:1',
            'planning' => 'sometimes|required|array',
            'planning.finance' => 'sometimes|required|in:1',
            'meeting' => 'sometimes|required|array',
            'meeting.sessions' => 'sometimes|required|in:1',
            'meeting.payments' => 'sometimes|required|in:1',
            'meeting.presences' => 'sometimes|required|in:1',
            'report' => 'sometimes|required|array',
            'report.session' => 'sometimes|required|in:1',
            'report.round' => 'sometimes|required|in:1',
        ]);

        if($validator->fails())
        {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
