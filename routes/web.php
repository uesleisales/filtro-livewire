<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;

/**
 * Rota principal - redireciona para produtos
 */
Route::get('/', function () {
    return redirect()->route('produtos.index');
})->name('home');

/**
 * Rota para página de produtos com filtros
 */
Route::get('/produtos', function () {
    return view('produtos.index');
})->name('produtos.index');

/*
 * Routes for category management
 */
Route::resource('categories', CategoryController::class);

/*
 * Routes for brand management
 */
Route::resource('brands', BrandController::class);

/**
 * Rota para visualizar produto específico (futura implementação)
 */
Route::get('/produtos/{id}', function ($id) {
    // Futura implementação para detalhes do produto
    return redirect()->route('produtos.index');
})->name('produtos.show');
