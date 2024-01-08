<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\MedicineController;


// // Public Routes
// Route::get('/', function () {
//     return view('login');
// })->name('login');

// Route::post('/login', [UsersController::class, 'loginAuth'])->name('login.auth');


Route::get('/error-permission', function () {
    return view('errors.permission');
})->name('error.permission');

//Cek sudah login
Route::middleware('IsGuest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    Route::post('/login', [UsersController::class, 'loginAuth'])->name('login.auth');
});

// Authenticated Routes
Route::middleware(['IsLogin'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home.page');
    Route::get('/logout', [UsersController::class, 'logout'])->name('logout');
});
    

Route::middleware(['IsLogin', 'IsKasir'])->group(function (){
    Route::prefix('/kasir')->name('kasir.')->group(function (){
        Route::prefix('/order')->name('order.')->group(function (){
            Route::get('/', [OrdersController::class, 'index'])->name('index');
            Route::get('/create', [OrdersController::class, 'create'])->name('create');
            Route::post('/store', [OrdersController::class, 'store'])->name('store');
            Route::get('/print/{id}', [OrdersController::class, 'show'])->name('print');
            Route::get('/download/{id}', [OrdersController::class, 'downloadPDF'])->name('download');
            Route::any('/filter', [OrdersController::class, 'filter'])->name('filter');
            
        });
    });
});
// Admin Routes
Route::middleware(['IsAdmin'])->group(function () {
    // Admin Home
    Route::get('/admin/home', function () {
        return view('admin.home');
    })->name('admin.home'); 
});

Route::middleware(['IsLogin', 'IsAdmin'])->group(function (){
    Route::prefix('/order')->name('order.')->group(function(){
        Route::get('/data', [OrdersController::class, 'data'])->name('data');
        Route::get('/export-excel', [OrdersController::class, 'exportExcel'])->name('export-excel');
    });
});

        // Medicine Routes
        Route::prefix('/medicine')->name('medicine.')->group(function () {
            Route::get('/create', [MedicineController::class, 'create'])->name('create');
            Route::post('/store', [MedicineController::class, 'store'])->name('store');
            Route::get('/', [MedicineController::class, 'index'])->name('home');
            Route::get('/stock', [MedicineController::class, 'stock'])->name('stock');
            Route::get('/{id}', [MedicineController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [MedicineController::class, 'update'])->name('update');
            Route::delete('/{id}', [MedicineController::class, 'destroy'])->name('delete');
            Route::get('/data/stock/{id}', [MedicineController::class, 'stockEdit'])->name('stock.edit');
            Route::patch('/data/stock/{id}', [MedicineController::class, 'stockUpdate'])->name('stock.update');
        });

        // User Routes
        Route::prefix('/user')->name('user.')->group(function () {
            Route::get('/create', [UsersController::class, 'create'])->name('create');
            Route::get('/', [UsersController::class, 'index'])->name('akun');
            Route::post('/store', [UsersController::class, 'store'])->name('store');
            Route::get('/{id}', [UsersController::class, 'edit'])->name('edit');
            Route::delete('/{id}', [UsersController::class, 'destroy'])->name('delete');
            Route::patch('/{id}', [UsersController::class, 'update'])->name('update');
        });
