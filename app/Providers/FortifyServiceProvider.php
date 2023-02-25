<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

use function view;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Login view
        Fortify::loginView(function() {
            return view('tontine.auth.login');
        });
        Fortify::registerView(function() {
            return view('tontine.auth.register');
        });
        Fortify::requestPasswordResetLinkView(function() {
            return view('tontine.auth.forgot-password');
        });
        Fortify::resetPasswordView(function($request) {
            return view('tontine.auth.reset-password', ['request' => $request]);
        });
        /*Fortify::verifyEmailView(function () {
            return view('tontine.auth.verify-email');
        });*/

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function(Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function(Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
