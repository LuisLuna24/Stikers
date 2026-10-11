<?php

use Illuminate\Support\Facades\Route;

Route::view('dashboard', 'dashboard')->name('dashboard');

Route::prefix('stikers')
    ->name('stikers.')
    ->group(function () {
        Route::view('/', 'modules.admin.stikers.index')
            ->name('index');

        Route::view('/create', 'modules.admin.stikers.form')
            ->name('create');

        Route::view('/{id}/edit', 'modules.admin.stikers.form')
            ->name('edit');
    });

Route::view('/design-requests', 'modules.admin.stikers.requests')->name('design-requests.index');


Route::prefix('sales')
    ->name('sales.')
    ->group(function () {
        Route::view('/', 'modules.admin.sales.index')
            ->name('index');

        Route::view('/{id}', 'modules.admin.sales.view')
            ->whereNumber('id')
            ->name('view');
    });

Route::prefix('customers')
    ->name('customers.')
    ->group(function () {
        Route::view('/', 'modules.admin.customers.index')
            ->name('index');

        Route::view('/create', 'modules.admin.customers.form')
            ->name('create');

        Route::view('/{id}/edit', 'modules.admin.customers.form')
            ->name('edit');
    });

Route::view('/catalogs/sizes', 'modules.admin.catalogs.sizes')
    ->name('catalogs.sizes');
