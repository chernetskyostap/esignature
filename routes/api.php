<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\SignatureController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::prefix('documents')
            ->name('documents.')
            ->group(function () {
                Route::get('/', [DocumentController::class, 'index'])->name('index');
                Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
                Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
                Route::post('/{document}/send', [DocumentController::class, 'send'])->name('send');
            });

        Route::prefix('signatures')
            ->name('signatures.')
            ->group(function () {
                Route::get('/requests', [SignatureController::class, 'getRequests'])->name('get-requests');
                Route::post('/{signatureRequest}/sign', [SignatureController::class, 'sign'])->name('sign');
            });
    });

