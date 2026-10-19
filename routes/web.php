<?php

use App\Models\Design;
use App\Models\StickerFinish;
use App\Models\StickerSize;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredDesigns = Design::query()
        ->active()
        ->with(['variants' => fn ($q) => $q->active()->with(['stickerSize', 'stickerFinish'])])
        ->latest('id')
        ->take(8)
        ->get();

    $sizes = StickerSize::query()->where('is_active', true)->orderBy('name')->get();
    $finishes = StickerFinish::query()->where('is_active', true)->orderBy('name')->get();

    return view('welcome', compact('featuredDesigns', 'sizes', 'finishes'));
})->name('home');

Route::get('/catalogo', function () {
    $designs = Design::query()
        ->active()
        ->with(['variants' => fn ($q) => $q->active()->with(['stickerSize', 'stickerFinish'])])
        ->orderBy('name')
        ->get();

    $sizes = StickerSize::query()->where('is_active', true)->orderBy('name')->get();
    $finishes = StickerFinish::query()->where('is_active', true)->orderBy('name')->get();

    return view('catalogs', compact('designs', 'sizes', 'finishes'));
})->name('catalogs');

Route::get('/catalogs', fn () => redirect()->route('catalogs'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
