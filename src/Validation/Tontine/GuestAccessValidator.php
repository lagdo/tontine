<?php

namespace Siak\Tontine\Validation\Tontine;

use Illuminate\Support\Facades\Validator;
use Siak\Tontine\Validation\AbstractValidator;
use Siak\Tontine\Validation\ValidationException;

class GuestAccessValidator extends AbstractValidator
{
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateItem(array $values): array
    {
        $validator = Validator::make($this->values($values), [
            'tontine' => 'sometimes|required|array',
            'tontine.members' => 'sometimes|required|in:1',
            'tontine.categories' => 'sometimes|required|in:1',
            'planning' => 'sometimes|required|array',
            'planning.sessions' => 'sometimes|required|in:1',
            'planning.pools' => 'sometimes|required|in:1',
            'planning.subscriptions' => 'sometimes|required|in:1',
            'meeting' => 'sometimes|required|array',
            'meeting.sessions' => 'sometimes|required|in:1',
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
