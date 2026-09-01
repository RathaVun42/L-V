<?php

namespace App\Http\Controllers;

use App\Classes\ImageClass;
use App\Http\Requests\loginReq;
use App\Http\Requests\registerReq;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(registerReq $request){
        $create_image = new ImageClass(directory: 'images/users');
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_image' => $create_image->store($request->image)
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
    public function login(loginReq $request){
        $user = User::where('email', $request->email )->first();
        if(!$user){
            throw ValidationException::withMessages([ // this line will throw the arr to laravel failed response.
                'email' => 'Email doesn\'t exist.'
            ]);
        }
        if(!$user->hasVerifiedEmail()){
            throw ValidationException::withMessages([ // this line will throw the arr to laravel failed response.
                'email' => 'Email is not verified.'
            ]);
        }
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password does not match.',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'message' => 'User signed in.',
            'user' => $user,
            'token' => $token
        ], 200);
    }
    public function logout(Request $request){
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        $user->tokens()->where('id', $currentToken->id)->delete();

        return response([
            'message' => 'User signed out'
        ]);
    }
}
