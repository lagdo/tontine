<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\UpdateUserPassword;
use App\Actions\UpdateUserProfile;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Login view
        Fortify::loginView(function() {
            return view('tontine.app.auth.login');
        });
        Fortify::registerView(function() {
            return view('tontine.app.auth.register');
        });
        Fortify::requestPasswordResetLinkView(function() {
            return view('tontine.app.auth.forgot-password');
        });
        Fortify::resetPasswordView(function($request) {
            return view('tontine.app.auth.reset-password', ['request' => $request]);
        });
        /*Fortify::verifyEmailView(function () {
            return view('tontine.app.auth.verify-email');
        });*/

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfile::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function(Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function(Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
