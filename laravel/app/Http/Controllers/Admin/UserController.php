<?php

namespace App\Http\Controllers\Admin;

use App\Classes\ImageClass;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $create_image = new ImageClass(directory: 'images/users');
        // 1. Validate the submitted data.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],
            'profile_image' => [
                'required','file','image','mimes:png,jpg,jpeg,webp'
            ]
        ]);

        // 2. Prevent this endpoint from creating another admin.
        if ($validated['role'] === 'admin') {
            throw ValidationException::withMessages([
                'role' => 'You cannot assign the admin role through this endpoint.',
            ]);
        }

        // 3. Find the requested role.
        $role = Role::where('name', $validated['role'])
            ->where('guard_name', 'web')
            ->firstOrFail();

        // 4. Create the user and assign the role.
        $user = DB::transaction(function () use ($validated, $role, $create_image) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'profile_image' => $create_image->store($validated['profile_image'])
            ]);

            $user->assignRole($role);

            return $user;
        });

        // 5. Generate a temporary signed verification URL.
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // 6. Send the verification email using your existing notification.
        try {
            $user->notify(
                new VerifyEmailNotification($verificationUrl)
            );
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'The user was created, but the verification email could not be sent. Please retry sending the verification email.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'email_verified_at' => $user->email_verified_at,
                ],
            ], 500);
        }

        // 7. Return the created user.
        return response()->json([
            'message' => 'Member created. A verification email has been sent.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'email_verified_at' => $user->email_verified_at,
                'profile_image' => $user->profile_image
            ],
        ], 201);
    }
}
