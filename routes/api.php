<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('company', [CompanyController::class, 'index'])->name('company');
Route::post('company', [CompanyController::class, 'store'])->name('company.store');
Route::post('users', [CompanyController::class, 'getAllDetails'])->name('getAllDetails');

Route::get('file', [FileController::class, 'index'])->name('file');
Route::post('file', [FileController::class, 'store'])->name('file.store');


