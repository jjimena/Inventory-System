<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Request;
use App\Http\Controllers\Dashboard\HubController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\OrderItemController;

// User
Route::group([], function () {
    Route::view('/', 'dashboard.index')->name('index');
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profiles.show');
    
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class); 
    
    Route::resource('orders', OrderController::class);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/dashboard/orders/{id}', [OrderController::class, 'show'])->name('dashboard.orders.show');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('orders/customer/{customerId}', [OrderController::class, 'customerOrders'])->name('orders.customer_orders');

    Route::resource('customers', CustomerController::class)->except(['show', 'update']);
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    Route::get('/dashboard/order-items', [OrderItemController::class, 'index'])->name('dashboard.order-items.list');
    Route::get('/dashboard/order-items/search', [OrderItemController::class, 'search'])->name('order-items.search');
    Route::post('/order-items/store-multiple', [OrderItemController::class, 'storeMultiple'])->name('order-items.store-multiple');
    
    Route::get('/dashboard/reports/report_form', [OrderItemController::class, 'showReportForm'])->name('reports.report_form');
    Route::get('/dashboard/reports/report_results', [OrderItemController::class, 'showReportForm'])->name('reports.report_results');
    Route::post('/reports/generate', [OrderItemController::class, 'generateReport'])->name('reports.generate');

    Route::post('/dashboard/order-items/{orderItemId}/set-pending', [OrderItemController::class, 'setOrderPending'])->name('order-items.set-pending');
    Route::post('/dashboard/order-items/approve/{orderItemId}', [OrderItemController::class, 'approveOrder'])->name('order-items.approve');
    Route::post('/dashboard/order-items/{id}/reject', [OrderItemController::class, 'reject'])->name('order-items.reject');
    Route::resource('order-items', OrderItemController::class);

    Route::get('/dashboard/order-items/payment/{orderItemId}', [PaymentController::class, 'showPaymentForm'])->name('order-items.payment');
    Route::post('/dashboard/order-items/payment/process/{orderItemId}', [PaymentController::class, 'processPayment'])->name('order-items.payment.process');
    Route::get('order-items/payment', [PaymentController::class, 'showPaymentForm'])->name('dashboard.order-items.payment');
    Route::post('order-items/payment/process', [PaymentController::class, 'processPayment'])->name('dashboard.order-items.payment.process');
    Route::get('/dashboard/order-items', [PaymentController::class, 'index'])->name('dashboard.order-items.index');
});

// Admin
Route::middleware('admin')->group(function () {
    Route::get('/api/products/names', [ProductController::class, 'getProductNames']);
    Route::resource('users', UserController::class);
});
