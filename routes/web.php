<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\JaxonController;
use App\Http\Middleware\AnnotationCache;
use App\Http\Middleware\TontineTenant;
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
        ->middleware([AnnotationCache::class, TontineTenant::class]);

    // Route to Jaxon controller
    Route::post('ajax', [JaxonController::class, 'jaxon'])->name('tontine.ajax')
        ->middleware([AnnotationCache::class, TontineTenant::class]);
});
