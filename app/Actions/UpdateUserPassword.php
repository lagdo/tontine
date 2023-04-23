<?php

namespace App\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Siak\Tontine\Model\User;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use Fortify\PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('password');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
