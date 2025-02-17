<?php

use App\Http\Controllers\clientes\clienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\bodegas\bodegaController;
use App\Http\Controllers\productos\productoController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');

    //endpoints para el cliente
    Route::get('/clientes/{idCliente}', [ClienteController::class,'clientePorId'])->middleware('auth:api')->name('obtenerClientesPorId');
    Route::get('/clientes', [ClienteController::class,'index'])->middleware('auth:api')->name('obtenerClientes');
    Route::post('/clientes/create', [ClienteController::class,'store'])->middleware('auth:api')->name('crearCliente');
    Route::put('/clientes/update', [ClienteController::class,'update'])->middleware('auth:api')->name('actualizarCliente');
    Route::delete('/clientes/delete', [ClienteController::class,'delete'])->middleware('auth:api')->name('borrarCliente');

    //endpoints para bodegas
    Route::get('/bodega', [bodegaController::class,'index'])->middleware('auth:api')->name('obtenerBodegas');
    Route::get('/bodega/{id}', [bodegaController::class,'bodegaId'])->middleware('auth:api')->name('obtenerBodegaId');
    Route::post('/bodega/create', [bodegaController::class,'store'])->middleware('auth:api')->name('crearBodega');
    Route::put('/bodega/update', [bodegaController::class,'update'])->middleware('auth:api')->name('actualizarBodega');
    Route::delete('/bodega/delete', [bodegaController::class,'delete'])->middleware('auth:api')->name('borrarBodega');

    //endpoints para productos
    Route::get('/producto/informe', [productoController::class,'getInforme'])->middleware('auth:api')->name('obtenerInforme');
    Route::get('/producto', [productoController::class,'getAllDatosEntrega'])->middleware('auth:api')->name('obtenerProductos');
    Route::get('/producto/{id}', [productoController::class,'getDatosEntregaById'])->middleware('auth:api')->name('obtenerProductoId');
    Route::post('/producto/create', [productoController::class,'storeDatosEntrega'])->middleware('auth:api')->name('crearProducto');
    Route::put('/producto/update/{id}', [productoController::class,'updateDatosEntrega'])->middleware('auth:api')->name('actualizarProducto');
    Route::delete('/producto/delete/{id}', [productoController::class,'deleteDatosEntrega'])->middleware('auth:api')->name('borrarProducto');

});