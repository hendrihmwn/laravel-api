<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController as AuthenticationController;
use App\Http\Controllers\ProductController as ProductController;
use App\Http\Controllers\VoucherController as VoucherController;

Route::controller(AuthenticationController::class)->group(function(){
    Route::post('v1/register', 'register');
    Route::post('v1/login', 'login');
    Route::middleware('auth:api')->get('v1/refresh', 'refresh');
});

Route::middleware('auth:api')->group( function () {
    Route::controller(ProductController::class)->group(function(){
        Route::get('v1/product', 'index');
        Route::post('v1/product', 'create');
        Route::get('v1/product/{id}', 'view');
        Route::put('v1/product/{id}', 'update');
        Route::delete('v1/product/{id}', 'destroy');
    });

    Route::controller(VoucherController::class)->group(function(){
        Route::get('v1/voucher', 'index');
        Route::post('v1/voucher', 'create');
        Route::get('v1/voucher/{id}', 'view');
        Route::put('v1/voucher/{id}', 'update');
        Route::delete('v1/voucher/{id}', 'destroy');
        Route::post('v1/voucher-apply', 'apply');
        Route::delete('v1/voucher-apply', 'remove_apply_voucher');
    });
});

