<?php

use App\Http\Controllers\Api\IssueController;
use Illuminate\Support\Facades\Route;

Route::prefix('issues')->group(function () {
    Route::get('/', [IssueController::class, 'index']);
    Route::post('/', [IssueController::class, 'store']);
    Route::get('/{id}', [IssueController::class, 'show']);
    Route::put('/{id}', [IssueController::class, 'update']);
});
