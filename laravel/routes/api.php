<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', function (int $id, string $hash) {
    if(!URL::hasValidSignature(request())){
        abort(403, 'Invalid or expired verification link.');
    }

    $user = User::find($id);
    if(!$user){
        abort(404, 'User not found.');
    }

    if(!hash_equals($hash, sha1($user->email))){
        abort(403, 'Invalid verification link.');
    }

    if($user->hasVerifiedEmail()){
        return redirect('http://localhost:5173/');
    }else{
        $user->markEmailAsVerified();
        return redirect('http://localhost:5173/');
    }
})->name('verification.verify');