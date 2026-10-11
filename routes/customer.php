<?php

use Illuminate\Support\Facades\Route;

Route::view('dashboard', 'dashboardcustomer')->name('dashboard');

Route::view('catalog', 'modules.customer.catalog')->name('catalog');

Route::prefix('purchase')
    ->name('purchase.')
    ->group(function () {
        Route::view('/', 'modules.customer.purchase.index')
            ->name('index');

        Route::view('/create', 'modules.customer.purchase.form')
            ->name('create');

        Route::view('/{order}', 'modules.customer.purchase.form')
            ->whereNumber('order')
            ->name('edit');
    });
