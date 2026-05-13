<?php

use App\Http\Controllers\Web\IssueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('issues.index');
});

Route::resource('issues', IssueController::class)->except(['destroy']);
