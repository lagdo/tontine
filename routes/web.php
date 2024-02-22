<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\JaxonAnnotations;
use App\Http\Middleware\SetAppLocale;
use App\Http\Middleware\SetAppTemplate;
use App\Http\Middleware\TontineTenant;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\RoutePath;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', TontineTenant::class, SetAppLocale::class, SetAppTemplate::class])
    ->prefix(LaravelLocalization::setLocale())
    ->group(function()
    {
        // Home page
        //----------------------------------
        Route::get('/', [IndexController::class, 'index'])
            ->name('tontine.home')
            ->middleware([JaxonAnnotations::class]);

        // User profile page
        //----------------------------------
        Route::get('/profile', [IndexController::class, 'profile'])
            ->name('user.profile');

        // Report pages
        //----------------------------------
        Route::get('/pdf/report/session/{sessionId}', [ReportController::class, 'session'])
            ->name('report.session');
        Route::get('/pdf/report/savings/{sessionId}', [ReportController::class, 'savings'])
            ->name('report.savings');
        Route::get('/pdf/report/round/{roundId}', [ReportController::class, 'round'])
            ->name('report.round');

        // Input forms page
        //----------------------------------
        Route::get('/pdf/entry/session/{sessionId}', [FormController::class, 'session'])
            ->name('entry.session');
        Route::get('/pdf/entry/{form}/{sessionId?}', [FormController::class, 'entry'])
            ->name('entry.form')->where('form', 'report|transactions');
    });

// Redefine Fortify routes with different HTTP verbs
//--------------------------------------------------------
// Profile Information...
$path = RoutePath::for('user-profile-information.update', '/user/profile-information');
Route::post($path, [ProfileInformationController::class, 'update'])
    ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')]);

// Passwords...
$path = RoutePath::for('user-password.update', '/user/password');
Route::post($path, [PasswordController::class, 'update'])
    ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')]);

// Logout
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
