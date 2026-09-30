<?php

use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;

Route::get('/', BerandaController::class)->name('beranda');
