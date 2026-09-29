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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/sent/reset-password-email', [AuthController::class, 'sendResetPasswordEmail']);
Route::post('/set/new-password', [AuthController::class, 'setNewPassword'])->name('set.new-password');
Route::get('/email/verify/{id}/{hash}', function (int $id, string $hash) {
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

    if ($user->hasVerifiedEmail()) {
        return redirect('http://localhost:5173/');
    } else {
        $user->markEmailAsVerified();
        return redirect('http://localhost:5173/');
    }
})->name('verification.verify');
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/change/password', [AuthController::class, 'changePassword']);
    Route::put('/update/user', [AuthController::class, 'updateUserInfo']);
    Route::put('/update/profile_image', [AuthController::class, 'updateProfileImage']);
    Route::get('/verify/token', [AuthController::class, 'verifyToken']);


});
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get(
        'products/by-categories',
        [ProductController::class, 'byCategories']
    );
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('menus', MenuController::class);
    // Pickup settings
    Route::get(
        'pickup-settings',
        [CanteenSettingController::class, 'show']
    );

    Route::put(
        'pickup-settings',
        [CanteenSettingController::class, 'update']
    );
    // pick up slot
    Route::get(
            'pickup-slots',
            [PickupSlotController::class, 'index']
        );


});

