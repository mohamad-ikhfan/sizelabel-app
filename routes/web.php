<?php

use Illuminate\Support\Facades\Route;

Route::get('download/{file}', function ($file) {
    return response()->download(storage_path('app/public/exports/' . $file));
})->name('download');