<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CategoryController,
    ProductController,
    UserController,
    TraderApplicationController,
    OrderController,
    ShippingAddressController,
    PaymentController,
    ShippingMethodController,
    ContactMessagesController,
    WishlistController,
    CartController
};
use App\Http\Controllers\Api\Admins\{
    AdminProductController,
    AdminCategoryController,
    AdminUserController,
    AdminTraderApplicationController,
    AdminDashboardController
};
use App\Models\Wishlist;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/admin-login', [AuthController::class, 'loginAsAdmin']);
Route::post('/auth/admin-register', [AuthController::class, 'registerasAdmin']);


// Public access to products, categories, etc. (optional)
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);

// Public access to shipping methods
Route::apiResource('shipping-methods', ShippingMethodController::class)->only(['index', 'show']);
Route::apiResource('traders-applications', TraderApplicationController::class);
Route::apiResource('contact', ContactMessagesController::class);
Route::apiResource('copons', \App\Http\Controllers\Api\CouponController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
Route::apiResource('users', UserController::class);
/*
|--------------------------------------------------------------------------
| Protected Routes (Require Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::delete('/auth/delete', [AuthController::class, 'deleteAccount']);

    // Full access to user-related resources

    Route::apiResource('orders', OrderController::class);
    Route::apiResource('shipping-addresses', ShippingAddressController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('whishlists', WishlistController::class);
    Route::get('carts/index', [CartController::class, 'index']);
    Route::post('carts/add-item', [CartController::class, 'addItem']);
    Route::put('carts/update-item/{id}', [CartController::class, 'updateItem']);
    Route::delete('carts/clear', [CartController::class, 'clear']);
});



/*
|--------------------------------------------------------------------------
| Admin Routes (Require is_admin Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index']);
    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('categories', AdminCategoryController::class);
    // Route::apiResource('users', AdminUserController::class);
    Route::apiResource('trader-applications', AdminTraderApplicationController::class);
});
