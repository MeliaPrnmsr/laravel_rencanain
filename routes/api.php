<?php

use Illuminate\Support\Facades\Route;

//workspaces
Route::apiResource('/workspaces', App\Http\Controllers\Api\ProjectPlannerController::class);