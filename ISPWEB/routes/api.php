<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\GracePeriodController;

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\BtrcLogController;
use App\Http\Controllers\Api\CustomGatewayController;
use App\Http\Controllers\Api\ResellerController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\BroadcastController;
use App\Http\Controllers\Api\PackageRequestController;
use App\Http\Controllers\Api\RouterControlController;
use App\Http\Controllers\Api\FtpMediaController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AccountingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Webhooks
Route::post('/webhook/sslcommerz', [PaymentWebhookController::class, 'sslcommerzIpn']);
Route::post('/webhook/bkash', [PaymentWebhookController::class, 'bkashCallback']);
Route::post('/webhook/custom-gateway', [CustomGatewayController::class, 'webhook']);
Route::post('/webhook/btrc-log', [BtrcLogController::class, 'receiveLog']);

// WhatsApp Chatbot Webhooks
Route::get('/webhook/whatsapp', [ChatbotController::class, 'verifyWebhook']);
Route::post('/webhook/whatsapp', [ChatbotController::class, 'handleMessage']);

// Customer APIs
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/customer/grace-period', [GracePeriodController::class, 'requestGracePeriod']);
    Route::post('/customer/ticket', [TicketController::class, 'submitTicket']);
    Route::post('/payment/initiate', [CustomGatewayController::class, 'initiatePayment']);
    
    // Self-Care
    Route::get('/customer/profile', [\App\Http\Controllers\Api\CustomerProfileController::class, 'getProfile']);
    Route::get('/customer/usage', [\App\Http\Controllers\Api\CustomerProfileController::class, 'getUsageHistory']);
    Route::get('/customer/billing-history', [\App\Http\Controllers\Api\CustomerProfileController::class, 'getBillingHistory']);
    Route::post('/customer/package-request', [PackageRequestController::class, 'requestChange']);
    Route::get('/customer/wifi', [RouterControlController::class, 'getWifiSettings']);
    Route::post('/customer/wifi', [RouterControlController::class, 'updateWifiSettings']);
    Route::post('/customer/wifi/block', [RouterControlController::class, 'blockDevice']);
    
    // Value Added Services
    Route::get('/customer/media-server', [FtpMediaController::class, 'getMovies']);
    Route::get('/customer/ott-packages', [\App\Http\Controllers\Api\OttController::class, 'getPackages']);
    Route::post('/customer/ott-subscribe', [\App\Http\Controllers\Api\OttController::class, 'subscribe']);
});

// Admin / Reseller APIs
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reseller/sub-account', [ResellerController::class, 'createSubAccount']);
    Route::post('/broadcast/sms-blast', [BroadcastController::class, 'blastSms']);
    Route::get('/technician/locations', [TicketController::class, 'getTechnicianLocations']);
    Route::post('/admin/package-request/{id}/approve', [PackageRequestController::class, 'approveChange']);
    
    // ERP - Inventory
    Route::post('/inventory/hardware/stock-in', [InventoryController::class, 'stockInHardware']);
    Route::post('/inventory/hardware/assign', [InventoryController::class, 'assignHardware']);
    Route::post('/inventory/cable/drum', [InventoryController::class, 'addCableDrum']);
    Route::post('/inventory/cable/usage', [InventoryController::class, 'recordCableUsage']);

    // ERP - Accounting
    Route::post('/accounting/expense', [AccountingController::class, 'recordExpense']);
    Route::get('/accounting/pnl', [AccountingController::class, 'generatePnL']);
});

// Technician APIs
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/technician/location', [TicketController::class, 'updateTechnicianLocation']);
});


