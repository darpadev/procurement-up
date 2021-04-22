<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\UserController;

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

// Prevent accessing home page without login
Route::middleware(['auth'])->group(function () {
    Route::get('/', function (){
        redirect(route('home'));
    });

    // HomeController
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ProcurementController
    Route::get('/new-procurement', [ProcurementController::class, 'create'])->name('new-procurement');
    Route::get('/my-procurement', [ProcurementController::class, 'index'])->name('my-procurement');
    Route::get('/my-procurement/show/{id}', [ProcurementController::class, 'show'])->name('show-procurement');
    Route::get('/my-procurement/edit/{id}', [ProcurementController::class, 'edit'])->name('edit-procurement');
    Route::get('/view-document/{id}', [ProcurementController::class, 'viewDoc'])->name('view-document');
    Route::get('/download-template', [ProcurementController::class, 'downloadTemplate'])->name('download-template');
        // Form
        Route::post('/store-procurement', [ProcurementController::class, 'store'])->name('store-procurement');
        Route::post('/update-procurement/{id}', [ProcurementController::class, 'update'])->name('update-procurement');
        Route::post('/doc-upload', [ProcurementController::class, 'docUpload']);
        Route::get('/doc-destroy/{proc}/{id}', [ProcurementController::class, 'docDestroy'])->name('doc-destroy');
        
    // UserController
    Route::get('/profile', [UserController::class, 'index'])->name('profile');
});

// AuthController
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');
    // Form 
    Route::post('/post-login', [AuthController::class, 'postLogin'])->name('post-login');
    Route::post('/store-account', [AuthController::class, 'storeAccount'])->name('store-account');


