<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CanteenSettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PickupSlotController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Admin\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware([
        'auth:sanctum',
        'permission:create users',
    ]);
Route::post('/sent/reset-password-email', [AuthController::class, 'sendResetPasswordEmail']);
Route::post('/set/new-password', [AuthController::class, 'setNewPassword'])->name('set.new-password');
Route::get(
    '/email/verify/{id}/{hash}',
    function (int $id, string $hash) {
        if (!URL::hasValidSignature(request())) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found.');
        }

        if (!hash_equals($hash, sha1($user->email))) {
            abort(403, 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect(
            env('REDIRECT_URL')."/email_verified?status=success"
        );
    }
)->name('verification.verify');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/change/password', [AuthController::class, 'changePassword']);
    Route::put('/update/user', [AuthController::class, 'updateUserInfo']);
    Route::put('/update/profile_image', [AuthController::class, 'updateProfileImage']);
    Route::get('/verify/token', [AuthController::class, 'verifyToken']);
});

Route::middleware('auth:sanctum')
    ->prefix('admin')
    ->group(function () {
        // Roles
        Route::get('roles', [AuthController::class, 'getRoles'])->middleware('permission:view roles');
        // Products
        Route::get('products', [ProductController::class, 'index'])
            ->middleware('permission:view products');

        Route::get('products/by-categories', [
            ProductController::class,
            'byCategories',
        ])->middleware('permission:view products');

        Route::post('products', [ProductController::class, 'store'])
            ->middleware('permission:create products');

        Route::put('products/{product}', [ProductController::class, 'update'])
            ->middleware('permission:update products');

        Route::patch('products/{product}', [ProductController::class, 'update'])
            ->middleware('permission:update products');

        Route::delete('products/{product}', [ProductController::class, 'destroy'])
            ->middleware('permission:delete products');

        // Categories
        Route::get('categories', [CategoryController::class, 'index'])
            ->middleware('permission:view categories');

        Route::post('categories', [CategoryController::class, 'store'])
            ->middleware('permission:create categories');

        Route::put('categories/{category}', [CategoryController::class, 'update'])
            ->middleware('permission:update categories');

        Route::patch('categories/{category}', [CategoryController::class, 'update'])
            ->middleware('permission:update categories');

        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
            ->middleware('permission:delete categories');

        // Menus
        Route::apiResource('menus', MenuController::class)
            ->middleware('permission:view menus');

        // Pickup settings
        Route::get('pickup-settings', [
            CanteenSettingController::class,
            'show',
        ])->middleware('permission:view pickup settings');

        Route::put('pickup-settings', [
            CanteenSettingController::class,
            'update',
        ])->middleware('permission:manage pickup settings');

        // Pickup slots
        Route::get('pickup-slots', [
            PickupSlotController::class,
            'index',
        ])->middleware('permission:view pickup slots');
    });
