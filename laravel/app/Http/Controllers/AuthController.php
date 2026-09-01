<?php

namespace App\Http\Controllers;

use App\Classes\ImageClass;
use App\Http\Requests\loginReq;
use App\Http\Requests\registerReq;
use App\Http\Requests\SendResetPasswordEmailRequest;
use App\Http\Requests\SetNewPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AuthController extends Controller
{
    public function register(registerReq $request)
    {
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
    public function login(loginReq $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([ // this line will throw the arr to laravel failed response.
                'email' => 'Email doesn\'t exist.'
            ]);
        }
        if (!$user->hasVerifiedEmail()) {
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
    public function logout(Request $request)
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        $user->tokens()->where('id', $currentToken->id)->delete();

        return response([
            'message' => 'User signed out'
        ]);
    }
    public function sendResetPasswordEmail(SendResetPasswordEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            throw ValidationException::withMessages([
                'email' => 'Email does not exist.'
            ]);
        }
        $status = Password::sendResetLink(
            ['email' => $request->email],
            function ($user, $token) use ($request) {
                $user->notify(new ResetPasswordNotification($token, $request->callback_url));
            }
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response([
                'message' => 'Password reset link sent to your email'
            ], 200);
        }
        return response([
            'message' => 'Password reset link sent to your email'
        ], 200);
    }
    public function setNewPassword(SetNewPasswordRequest $request)
    {
        $status = Password::reset(
            [
                'token' => $request->token,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_comfirmation,
            ],
            function ($user, $password) {
                $user->password = $password;
                $user->save();
                $user->tokens()->delete();
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'password' => [__($status)]
            ]);
        }
        return response([
            'message' => 'Password has been reset successfully.'
        ], 200);
    }
}
