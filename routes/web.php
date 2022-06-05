<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\JaxonController;
use App\Http\Controllers\WebController;
use App\Http\Middleware\AnnotationCache;
use App\Http\Middleware\Tontine;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
        ->middleware([AnnotationCache::class, Tontine::class]);

    // Route to Jaxon controller
    Route::post('ajax', [JaxonController::class, 'jaxon'])->name('tontine.ajax')
        ->middleware([AnnotationCache::class, Tontine::class]);

    // Deposit page
    //----------------------------------
    Route::get('/deposit/{deposit}', [WebController::class, 'showDepositPage'])
        ->name('tontine.deposit.page');
});
