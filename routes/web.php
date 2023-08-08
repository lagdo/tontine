<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\JaxonController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AnnotationCache;
use App\Http\Middleware\SetDateFormat;
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

Route::group(['prefix' => LaravelLocalization::setLocale()], function()
{
    // Home page
    //----------------------------------
    Route::get('/', [IndexController::class, 'index'])->name('tontine.home')
        ->middleware(['auth', AnnotationCache::class, TontineTenant::class, SetDateFormat::class]);

    // Route to handle Jaxon ajax requests
    //----------------------------------
    Route::post('ajax', [JaxonController::class, 'jaxon'])->name('tontine.ajax')
        ->middleware(['auth', AnnotationCache::class, TontineTenant::class, SetDateFormat::class]);

    // User profile page
    //----------------------------------
    Route::get('/profile', [IndexController::class, 'profile'])->name('user.profile')
        ->middleware(['auth', TontineTenant::class, SetDateFormat::class]);

    // Report pages
    //----------------------------------
    Route::get('/report/session/{sessionId}', [ReportController::class, 'session'])
        ->name('report.session')->middleware(['auth', TontineTenant::class, SetDateFormat::class]);
    Route::get('/report/round', [ReportController::class, 'round'])
        ->name('report.round')->middleware(['auth', TontineTenant::class, SetDateFormat::class]);
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
