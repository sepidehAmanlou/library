<?php

use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\LanguageController;


Route::prefix('/book_categories')->name('bookCategories.')->middleware('auth.token')->group( function(){

    Route::get('/',[BookCategoryController::class,'index'])->name('index');
    Route::get('/{bookCategory}',[BookCategoryController::class,'show'])->name('show');

    Route::middleware('check.admin')->group(function(){

    Route::post('/',[BookCategoryController::class,'store'])->name('store');
    Route::put('/{bookCategory}',[BookCategoryController::class,'update'])->name('update');
    Route::delete('/{bookCategory}',[BookCategoryController::class,'destroy'])->name('destroy');
    });
});

Route::prefix('/books')->name('books.')->middleware('auth.token')->group( function(){

    Route::get('/',[BookController::class,'index'])->name('index');
    Route::get('/{book}',[BookController::class,'show'])->name('show');

    Route::middleware('check.admin')->group(function(){

    Route::post('/',[BookController::class,'store'])->name('store');
    Route::put('/{book}',[BookController::class,'update'])->name('update');
    Route::delete('/{book}',[BookController::class,'destroy'])->name('destroy');
    });

});

Route::prefix('/user_categories')->name('userCategory.')->middleware('auth.token')->group( function(){
    
    Route::get('/',[UserCategoryController::class,'index'])->name('index');
    Route::get('/{userCategory}',[UserCategoryController::class,'show'])->name('show');

    Route::middleware('check.admin')->group(function(){

    Route::post('/',[UserCategoryController::class,'store'])->name('store');
    Route::put('/{userCategory}',[UserCategoryController::class,'update'])->name('update');
    Route::delete('/{userCategory}',[UserCategoryController::class,'destroy'])->name('destroy');
    });
});

Route::prefix('my_books')->middleware('auth.token')->name('myBook')->group(function(){

    Route::get('/',[BookUserController::class,'index'])->name('index');
    Route::post('/{book}',[BookUserController::class,'store'])->name('store');
    Route::put('/{book}',[BookUserController::class,'update'])->name('update');
    Route::delete('/{book}',[BookUserController::class,'destroy'])->name('destroy');
});

Route::prefix('users')->middleware('auth.token')->name('users.')->group(function(){
    Route::put('/{user}',[UserController::class,'update'])->name('update');

    Route::middleware('check.admin')->group(function(){

    Route::get('/',[UserController::class,'index'])->name('index');
    Route::post('/',[UserController::class,'store'])->name('store');
     Route::get('/{user}',[UserController::class,'show'])->name('show');
    Route::delete('/{user}',[UserController::class,'destroy'])->name('destroy');
    });
});

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth.token')->group(function () {
        Route::get('/profile', function(Request $request) {
            return $request->auth_user;
        });
    });

Route::prefix('/carts')->name('carts.')->middleware('auth.token')->group(function(){

    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::get('/{cart}', [CartController::class, 'show'])->name('show');
    Route::put('/{cart}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cart}', [CartController::class, 'destroy'])->name('destroy');

});

Route::prefix('/orders')->name('orders.')->middleware('auth.token')->group(function(){
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');

    Route::middleware('check.admin')->group(function(){

    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('/order_items')->name('orderItems.')->middleware('auth.token')->group(function(){

    Route::get('/', [OrderItemController::class, 'index'])->name('index');
    Route::post('/', [OrderItemController::class, 'store'])->name('store');
    Route::get('/{orderItem}', [OrderItemController::class, 'show'])->name('show');

    Route::middleware('check.admin')->group(function(){

    Route::put('/{orderItem}', [OrderItemController::class, 'update'])->name('update');
    Route::delete('/{orderItem}', [OrderItemController::class, 'destroy'])->name('destroy');
    });

});

Route::prefix('/payments')->name('payments.')->middleware('auth.token')->group(function(){

    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
    Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');

    Route::middleware('check.admin')->group(function(){

    Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
    Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
    });

});

Route::get('/callback', [PaymentController::class, 'callback'])->name('callback');


Route::prefix('/languages')->name('languages.')->group(function () {
    
    Route::get('/', [LanguageController::class, 'index'])->name('index');
    Route::get('/{language}', [LanguageController::class, 'show'])->name('show');

    Route::middleware(['auth.token', 'check.admin'])->group(function () {

        Route::post('/', [LanguageController::class, 'store'])->name('store');
        Route::put('/{language}', [LanguageController::class, 'update'])->name('update');
        Route::delete('/{language}', [LanguageController::class, 'destroy'])->name('destroy');
    });
});


Route::get('/install/check', [InstallerController::class, 'checkInstallation'])->name('checkInstallation');
Route::post('/install', [InstallerController::class, 'install'])->name('install');


