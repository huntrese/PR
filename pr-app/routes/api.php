<?php

use App\Livewire\Welcome;
use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileUploadController;


Route::post('/upload-json', [FileUploadController::class, 'uploadJson']);

use App\Http\Middleware\FileLockMiddleware;


Route::middleware(FileLockMiddleware::class)->group(function () {
    Route::post('/write-to-file', [FileController::class, 'writeToFile']);
    Route::get('/read-from-file', [FileController::class, 'readFromFile']);
});
