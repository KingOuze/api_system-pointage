<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

