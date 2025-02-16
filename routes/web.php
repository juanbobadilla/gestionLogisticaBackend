<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\clientes\clienteController;

Route::get('/', function () {
    return view('welcome');
});
