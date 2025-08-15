<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Food_Controller;
use App\Http\Controllers\Api\Payment_Contoller;
use App\Http\Controllers\Employee_Controller;
use App\Http\Controllers\login_controller;
use App\Http\Controllers\Api\Categories;
use App\Http\Controllers\Api\Orders_Contoller;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EmployeeMiddleware;
use App\Http\Controllers\Api\DashboardController;

// ------------------- Public / Login -------------------
Route::post('/login/session', [login_controller::class, 'login']);
// routes/api.php
Route::middleware(['auth:sanctum'])->get('/get-profile', [login_controller::class, 'getProfile']);

// ------------------- Admin Routes -------------------
Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {

    // Categories
    Route::post('/categories/store', [Categories::class, 'store']);

    Route::put('/categories/{id}', [Categories::class, 'update']);
    Route::delete('/categories/delete/{id}', [Categories::class, 'destroy']);

    // Food
    Route::post('/food/store', [Food_Controller::class, 'Store']);
    Route::put('/food/update/{id}', [Food_Controller::class, 'update']);
    Route::delete('/food/delete/{id}', [Food_Controller::class, 'destroy']);

    // Orders
    Route::put('/Orders/{order}/status', [Orders_Contoller::class, 'updateStatus']);

    Route::post('/Employee/Store', [Employee_Controller::class, 'Store']);
    Route::get('/Employee', [Employee_Controller::class, 'fetdata']);
    Route::put('/Employee/edit/{id}', [Employee_Controller::class, 'update']);
    Route::delete('Employee/delete/{id}', [Employee_Controller::class, 'delete']);
    // Payment
    Route::post('/Payment/getOrders', [Payment_Contoller::class, 'getOrders']);
    Route::post('/updatePayment', [Payment_Contoller::class, 'updatePayment']);
});

// ------------------- Employee (User) Routes -------------------
Route::middleware(['auth:sanctum', 'employee'])->group(function () {

    // Orders
    Route::post('/Orders/store', [Orders_Contoller::class, 'store']);

    // Employee management

});

// ------------------- Public / Common -------------------
Route::get('/food', [Food_Controller::class, 'index']);
Route::get('/categories', [Categories::class, 'index']);
Route::get('/Orders/getData', [Orders_Contoller::class, 'getData']);

Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
Route::get('/dashboard/sales/daily', [DashboardController::class, 'dailySales']);
Route::get('/dashboard/sales/weekly', [DashboardController::class, 'weeklySales']);
Route::get('/dashboard/sales/monthly', [DashboardController::class, 'monthlySales']);
