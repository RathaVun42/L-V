<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerReq;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function register(registerReq $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
         ]);

        $verificationUrl = URL::temporarySignedRoute( 
            'verification.verify', // this is a named route and it acts as reference route for our temporatySignedRoute
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );
        $user->notify(new VerifyEmailNotification($verificationUrl));
         return response()->json([
            'message' => 'Registration siccessful.'
         ], 201);
    }
}
