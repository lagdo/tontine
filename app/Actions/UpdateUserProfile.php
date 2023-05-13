<?php

namespace App\Actions;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Siak\Tontine\Model\User;

class UpdateUserProfile implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'size:2'],
        ])->validateWithBag('profile');

        $user->forceFill([
            'name' => $input['name'],
        ])->save();

        $user->profile ? $user->profile->fill([
            'city' => $input['city'] ?? '',
            'country_code' => $input['country'],
        ])->save() : $user->profile()->create([
            'city' => $input['city'] ?? '',
            'country_code' => $input['country'],
        ]);
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->profile ? $user->profile->fill([
            'country_code' => $input['country'],
        ])->save() : $user->profile()->create([
            'country_code' => $input['country'],
        ]);

        $user->sendEmailVerificationNotification();
    }
}
