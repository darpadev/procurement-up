<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BidderListController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\CategoryController;

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
    // HomeController
    Route::get('/', [HomeController::class, 'index'])->name('home');

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
        Route::post('/item/add-category/{id}', [ProcurementController::class, 'addItemCategory'])->name('add-item-category');
        Route::post('/doc-upload', [ProcurementController::class, 'docUpload']);
        Route::post('/item/add-vendor', [ProcurementController::class, 'addItemVendor'])->name('add-item-vendor');
        Route::post('/item/delete-vendor', [ProcurementController::class, 'deleteItemVendor'])->name('delete-item-vendor');
        Route::get('/doc-destroy/{proc}/{id}', [ProcurementController::class, 'docDestroy'])->name('doc-destroy');
        
    // UserController
    Route::get('/profile', [UserController::class, 'index'])->name('profile');

    // BidderListController
    Route::get('/bidder-list', [BidderListController::class, 'index'])->name('bidder-list');
    Route::get('/bidder-list/new', [BidderListController::class, 'create'])->name('new-vendor');
        // Form
        Route::post('/store-vendor', [BidderListController::class, 'store'])->name('store-vendor');
        Route::post('/update-vendor/{id}', [BidderListController::class, 'update'])->name('update-vendor');
        Route::get('/destroy/{vendor}/{category}/{sub_category}', [BidderListController::class, 'destroyVendorCategory'])->name('destroy-vendor-category');
        // AJAX
        Route::post('/get-sub-category', [BidderListController::class, 'getSubCategory']);
});

// DocumentController
    // Request Form
    Route::get('/generate-spph/form/{proc_id}/{vendor_id}', [DocumentController::class, 'generateSpphForm'])->name('generate-spph-form');
    Route::get('/generate-bapp/form/{proc_id}/{vendor_id}', [DocumentController::class, 'generateBappForm'])->name('generate-bapp-form');

    // Upload Document
    Route::post('/upload/{name}', [DocumentController::class, 'upload'])->name('upload');

    // Export Document 
    Route::post('/generate-spph', [DocumentController::class, 'generateSpph'])->name('generate-spph');
    Route::post('/generate-bapp', [DocumentController::class, 'generateBapp'])->name('generate-bapp');
    Route::get('/view/{id}/{table}', [DocumentController::class, 'view'])->name('view-document-vendor');

    // Declare Winning Quotation
    Route::post('/set/winner', [DocumentController::class, 'setWinner'])->name('set-winner');

// AuthController
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');
    // Form 
    Route::post('/post-login', [AuthController::class, 'postLogin'])->name('post-login');
    Route::post('/store-account', [AuthController::class, 'storeAccount'])->name('store-account');

// AJAX
Route::post('/get-sub-category', [CategoryController::class, 'getSubCategory']);