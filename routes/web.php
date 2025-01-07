<?php

use Alisons\Caller\Http\Controllers\CallerController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->group(
    function () {
        Route::resource('caller', CallerController::class)->names('caller');
        // Route::post('update_settings', [CallerController::class, 'update_settings'])->name('update.settings');
        Route::get('settings', [CallerController::class, 'getCallerSetting'])->name('caller.settings');

        Route::get('dialer', [CallerController::class, 'dailer'])->name('caller.hello');

        Route::get('pop', [CallerController::class, 'callerPop'])->name('caller.pop');
    }
);
