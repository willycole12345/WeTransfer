<?php

use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

  Route::post('/welcome', [UploadController::class, 'create'])->name('welcome');
  Route::post('/upload', [UploadController::class, 'store'])->name('upload');
  Route::post('/download/{token}', [UploadController::class, 'view'])->name('download');
  Route::get('/stats/{token}', [UploadController::class, 'Get_Uploaded_Details'])->name('stats');
