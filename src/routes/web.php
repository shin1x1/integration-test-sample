<?php

use App\Http\Controllers\Task\CreateTaskAction;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/tasks', CreateTaskAction::class);
