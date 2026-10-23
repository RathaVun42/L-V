<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexUsersRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    public function index(IndexUsersRequest $request)
    {
        $validated = $request->validated();

        $users = User::query()
            ->with('roles')
            ->when($validated['search'] ?? null, function ($query, $search) {
                // The syntax of when:
                //$query->when($condition, function ($query, $value) {
                    // Modify the query only when the condition is truthy.
                // });
                // If the condition is truthy, Laravel runs the callback. If it's falsey, Laravel skips it.
                // The callback's second parameter, $search, receives the value that made the condition truthy.
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($validated['role'] ?? null, function ($query, $role) {
                $query->role($role);
                //selects users assigned the $role role.
                //This works because your User model uses Spatie's HasRoles trait, which provides the role() query scope.
            })
            ->latest() //By default, this sorts the results by created_at in descending order
            ->paginate($validated['per_page'] ?? 10) // paginate() is the point where Laravel executes the database query. it's the same to get()
            ->withQueryString();
            // When you move to the next page, the pagination link retains those filters.
            // Without withQueryString() method, Laravel's generated pagination links might not retain all your search and filter parameters.
            // That matters because you don't want the Admin Users page to lose its search when you click page 2.


            
        return UserResource::collection($users);
            // returned $users with pagination metadata
            // {
            // "data": [
            //     {
            //     "id": 12,
            //     "name": "Anna",
            //     "email": "anna@gmail.com",
            //     "roles": ["staff"],
            //     "email_verified_at": "2026-10-20T03:00:00.000000Z"
            //     }
            // ],
            // "links": {
            //     "first": "...",
            //     "last": "...",
            //     "prev": null,
            //     "next": "..."
            // },
            // "meta": {
            //     "current_page": 1,
            //     "per_page": 10,
            //     "total": 1
            // }
            // }
    }



// Inside AdminUserController:

public function updateRole(
    UpdateUserRoleRequest $request,
    User $user
) {
    $role = $request->validated()['role'];

    DB::transaction(function () use ($user, $role) {
        $user->syncRoles([$role]);
    });

    return response()->json([
        'message' => 'User role updated successfully.',
        'user' => new UserResource($user->fresh()),
    ]);
}

}
