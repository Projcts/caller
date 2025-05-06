<?php

use Alisons\Caller\Http\Controllers\CallerController;
use Alisons\Caller\Http\Controllers\CallLogController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->group(
    function () {
        Route::resource('caller', CallerController::class)->names('caller');

        Route::post('generate-logs', [CallerController::class, 'generate_log'])->name('caller.generatelog');
        // Route::post('update_settings', [CallerController::class, 'update_settings'])->name('update.settings');
        Route::get('settings', [CallerController::class, 'getCallerSetting'])->name('caller.settings');
        Route::get('get-call-settings', [CallerController::class, 'settings'])->name('caller.getsettings');
        Route::get('dialer', [CallerController::class, 'dailer'])->name('caller.hello');
        Route::get('export', [CallerController::class, 'exportCsv'])->name('caller.exportcsv');
        Route::get('call_logs', [CallerController::class, 'getLogs'])->name('caller.getlogs');
        Route::get('call-logs/export', [CallerController::class, 'exportLogs'])->name('call-logs.export');
        Route::get('pop', [CallerController::class, 'callerPop'])->name('caller.pop');
        Route::get('call-summary', [CallerController::class, 'callSummary'])->name('caller.summary');
    }
);
