<?php

use App\Http\Controllers\BracketController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Game Scheduling & Bracketing routes for Barangay Taysan
|--------------------------------------------------------------------------
| Paste these into your project's routes/web.php (or require this file
| from there). Wrap them in your existing auth/admin middleware group
| if only barangay staff should manage teams and brackets.
*/

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

Route::get('/brackets/create', [BracketController::class, 'create'])->name('brackets.create');
Route::post('/brackets', [BracketController::class, 'store'])->name('brackets.store');
Route::get('/brackets/{bracket}', [BracketController::class, 'show'])->name('brackets.show');

Route::post('/matches/{match}/schedule', [MatchController::class, 'schedule'])->name('matches.schedule');
Route::post('/matches/{match}/result', [MatchController::class, 'recordResult'])->name('matches.result');
