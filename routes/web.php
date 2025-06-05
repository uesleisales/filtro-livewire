<?php

use Illuminate\Support\Facades\Route;

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

/**
 * Rota para visualizar produto específico (futura implementação)
 */
Route::get('/produtos/{id}', function ($id) {
    // Futura implementação para detalhes do produto
    return redirect()->route('produtos.index');
})->name('produtos.show');

/**
 * Rotas para desenvolvimento/debug (remover em produção)
 */
if (app()->environment('local')) {
    Route::get('/debug/categories', function () {
        return \App\Models\Category::with('products')->get();
    });
    
    Route::get('/debug/brands', function () {
        return \App\Models\Brand::with('products')->get();
    });
    
    Route::get('/debug/products', function () {
        return \App\Models\Product::with(['category', 'brand'])->get();
    });
}
