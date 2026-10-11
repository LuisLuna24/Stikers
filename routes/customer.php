<?php

use Illuminate\Support\Facades\Route;

Route::view('dashboard', 'dashboardcustomer')->name('dashboard');

Route::view('catalog', 'modules.customer.catalog')->name('catalog');
