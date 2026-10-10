<?php

use Illuminate\Support\Facades\Route;

Route::view('dashboard', 'dashboard')->name('dashboard');

Route::prefix('stikers')
    ->name('stikers.')
    ->group(function () {
        Route::view('/', 'modules.stikers.index')
            ->name('index');

        Route::view('/create', 'modules.stikers.form')
            ->name('create');

        Route::view('/{id}/edit', 'modules.stikers.form')
            ->name('edit');
    });


Route::prefix('sales')
    ->name('sales.')
    ->group(function () {
        Route::view('/', 'modules.sales.index')
            ->name('index');
    });

Route::prefix('customers')
    ->name('customers.')
    ->group(function () {
        Route::view('/', 'modules.customers.index')
            ->name('index');

        Route::view('/create', 'modules.customers.form')
            ->name('create');

        Route::view('/{id}/edit', 'modules.customers.form')
            ->name('edit');
    });

Route::prefix('catalogs')
    ->name('catalogs.')
    ->group(function () {
        Route::view('/sizes', 'modules.catalogs.sizes')
            ->name('sizes');

        Route::view('/finishes', 'modules.catalogs.finishes')
            ->name('finishes');
    });
