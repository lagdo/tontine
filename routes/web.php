<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\JaxonController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AnnotationCache;
use App\Http\Middleware\TontineTenant;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
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

    // Report pages
    //----------------------------------
    Route::get('/report/pool/{poolId}', [ReportController::class, 'pool'])
        ->name('report.pool')->middleware(['auth', TontineTenant::class]);
    Route::get('/report/session/{sessionId}', [ReportController::class, 'session'])
        ->name('report.session')->middleware(['auth', TontineTenant::class]);
});

// Logout
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
