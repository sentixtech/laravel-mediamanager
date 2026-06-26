<?php

use Illuminate\Support\Facades\Route;
use Sentix\MediaManager\Controller\MediaController;
use Sentix\MediaManager\Middleware\AssignGuard;
use Sentix\MediaManager\Middleware\CheckMediaPermissions;

Route::group([
    'prefix' => config('media.routes.prefix', 'media'),
    'as' => 'media.',
    'middleware' => ['web', AssignGuard::class],
], function () {
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload')->middleware(CheckMediaPermissions::class.':upload');
    Route::post('/fetch', [MediaController::class, 'fetch'])->name('fetch')->middleware(CheckMediaPermissions::class.':fetch');
    Route::delete('/delete/{id?}', [MediaController::class, 'destroy'])->name('delete')->middleware(CheckMediaPermissions::class.':delete');
    Route::post('/bulk-delete', [MediaController::class, 'bulkDestroy'])->name('bulk-delete')->middleware(CheckMediaPermissions::class.':bulk_delete');
});
