<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['throttle:sms'])->post('payment-confirmation-callback', [PaymentController::class, 'stkcallback'])->name('payments.stkcallback');
Route::middleware(['throttle:sms'])->post('c2b-receiver-url', [PaymentController::class, 'c2bProcessor'])->name('payments.c2bProcessor');
Route::middleware(['throttle:sms'])->post('c2b-validation-url', [PaymentController::class, 'c2bValidation'])->name('payments.c2bValidation');
Route::middleware(['throttle:sms'])->post('buni-payments-notify', [PaymentController::class, 'kcbIpn'])->name('payments.buni.ipn');

// Public Route
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require the Bearer Token from Flutter)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('customers', \App\Http\Controllers\Api\CustomerController::class);
    Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class);
    Route::apiResource('invoices', \App\Http\Controllers\Api\InvoiceController::class);
    Route::get('/invoice-dependencies', [\App\Http\Controllers\Api\InvoiceController::class, 'dependencies']);

    Route::post('/invoices/{id}/send-reminder', [\App\Http\Controllers\Api\InvoiceController::class, 'sendReminder']);
    Route::put('/invoices/{id}/deliver', [\App\Http\Controllers\Api\InvoiceController::class, 'markAsDelivered']);
    Route::post('/invoices/{id}/update-status', [\App\Http\Controllers\Api\InvoiceController::class, 'updateStatus']);
    Route::get('/invoices/{id}/pdf', [\App\Http\Controllers\Api\InvoiceController::class, 'downloadPdf']);

    Route::post('/payments/stk-push', [\App\Http\Controllers\PaymentController::class, 'mpesaTopUp']);
    Route::post('/payments/stk-status', [\App\Http\Controllers\PaymentController::class, 'checkTransactionStatus']);
    Route::get('/payments/{id}/receipt', [\App\Http\Controllers\Api\PaymentController::class, 'downloadReceipt']);
    Route::apiResource('payments', \App\Http\Controllers\Api\PaymentController::class);

    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    Route::get('/reports', [\App\Http\Controllers\Api\ReportController::class, 'index']);
});
