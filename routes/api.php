<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubmissionController;

Route::post('/submit', [SubmissionController::class, 'store']);