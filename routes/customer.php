<?php

use Illuminate\Support\Facades\Route;

Route::view('dashboard', 'modules.customer.dashboard')->name('dashboard');

Route::get('catalog', fn () => redirect()->route('customer.purchase.create'))->name('catalog');

Route::view('design-requests', 'modules.customer.design-requests')->name('design-requests.index');

Route::prefix('purchase')
    ->name('purchase.')
    ->group(function () {
        Route::view('/', 'modules.customer.purchase.index')
            ->name('index');

        Route::view('/create', 'modules.customer.purchase.form')
            ->name('create');

        Route::view('/{order}', 'modules.customer.purchase.view')
            ->whereNumber('order')
            ->name('show');
    });
