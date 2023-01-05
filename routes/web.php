<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // $exitCode = Artisan::call('migrate:reset', [
    //     '--force' => true,
        
    // ]);
    // Schema::dropIfExists('migrations');
    // // dd($exitCode);
    // // DROP TABLE `migrations`;
    // DB::unprepared(Files::get('/home/acquaint/Downloads/billing_new.sql'));
    

    // $sql_dump = Files::get('/home/acquaint/Downloads/reportsystem.sql');
    // DB::connection()->getPdo()->exec($sql_dump);


        //ENTER THE RELEVANT INFO BELOW
        
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('file', [FileController::class, 'index'])->name('file');
    Route::get('file/download', [FileController::class, 'downloadFile'])->name('file.download');
    Route::post('file', [FileController::class, 'store'])->name('file.store');

    Route::get('company', [CompanyController::class, 'index'])->name('company');
    Route::get('company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('company/store', [CompanyController::class, 'store'])->name('company.store');
    Route::get('company/getUserData', [CompanyController::class, 'getUserData'])->name('company.getUserData');
    Route::get('company/edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('company/update/{id}', [CompanyController::class, 'update'])->name('company.update');

    Route::get('user/company/index',[UserController::class,'index'])->name('user.company.index');
    Route::get('user/company',[UserController::class,'create'])->name('user.company.create');
    Route::post('user/company/store', [UserController::class, 'store'])->name('user.company.store');
    Route::get('user/company/edit/{id}', [UserController::class, 'edit'])->name('user.company.edit');
    Route::put('user/company/update/{id}', [UserController::class, 'update'])->name('user.company.update');

});
Route::get('multidb_2', [UserController::class, 'multidb_2'])->name('multidb_2');
Route::get('multiconnection', [UserController::class, 'multiDB'])->name('multiDB');

require __DIR__.'/auth.php';
