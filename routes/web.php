<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('health', HealthCheckResultsController::class);

// key available in lang/fr.json and not in lang/vendor/nova/fr.json
Route::get('trigger', fn () => __('All rights reserved.'));
