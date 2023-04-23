<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\JaxonController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AnnotationCache;
use App\Http\Middleware\TontineTenant;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;

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
        ->middleware(['auth', AnnotationCache::class, TontineTenant::class]);

    // Route to Jaxon controller
    Route::post('ajax', [JaxonController::class, 'jaxon'])->name('tontine.ajax')
        ->middleware(['auth', AnnotationCache::class, TontineTenant::class]);

    // User profile page
    //----------------------------------
    Route::get('/profile', [IndexController::class, 'profile'])->name('user.profile')
        ->middleware(['auth', TontineTenant::class]);

    // Report pages
    //----------------------------------
    Route::get('/report/pool/{poolId}', [ReportController::class, 'pool'])
        ->name('report.pool')->middleware(['auth', TontineTenant::class]);
    Route::get('/report/session/{sessionId}', [ReportController::class, 'session'])
        ->name('report.session')->middleware(['auth', TontineTenant::class]);
});

// Redefine Fortify routes with with different HTTP verbs
//--------------------------------------------------------
// Profile Information...
Route::post('/user/profile-information', [ProfileInformationController::class, 'update'])
    ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')]);

// Passwords...
Route::post('/user/password', [PasswordController::class, 'update'])
    ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')]);

// Logout
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
