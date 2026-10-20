<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_image' => $this->profile_image,
            'password_null' => is_null($this->password),

            'roles' => $this->getRoleNames() //this will give a collection of role collect(['staff']); it's a collection because user can have multiple roles.
            ->values(), //values() resets the collection's keys to sequential numeric indexes, starting from zero. collect([
                                                                                                                    //     2 => 'staff',
                                                                                                                    //     5 => 'supervisor',
                                                                                                                    // ])->values();

                                                                                                                    // result

                                                                                                                //     collect([
                                                                                                                //     0 => 'staff',
                                                                                                                //     1 => 'supervisor',
                                                                                                                // ]);

                                                                                                                // final result "roles": ["staff", "supervisor"]
                                                                                                                // it's just normal arr of string because JSON serialization, Converts the collection into a JSON array
                                                                                                                // ** When your resource returns the collection in an API response, Laravel serializes it into JSON:

            'permissions' => $this->getAllPermissions() // this returns a collection of permission model objects, not just string 
                                                        //collect([
                                                        //     Permission(id: 1, name: 'view products'),
                                                        //     Permission(id: 2, name: 'view categories'),
                                                        //     Permission(id: 3, name: 'view menus'),
                                                        // ]);
                ->pluck('name') // mean បេះ​ ​pluck() extracts one field from every item in a collection.
                ->values(),

            'email_verified_at' => $this->email_verified_at,
        ];
    }
// whenever we use this resource, laravvel will automatically wrap thier output in a data key. 
// When you use new UserResource($user) inside your custom login response,
// the response will generally look like this:
// {
//   "message": "User signed in.",
//   "user": {
//     "data": {
//       "id": 1,
//       "name": "Sokha",
//       "email": "sokha@example.com",
//       "roles": ["customer"],
//       "permissions": []
//     }
//   },
//   "token": "..."
//  }
// * to unwrap it we can config in AppServiceProvider, just once for every resource
// use Illuminate\Http\Resources\Json\JsonResource;

// public function boot(): void
// {
//     JsonResource::withoutWrapping();
// }
// * or manually unwrap just add this function into that resource
// public static function withoutWrapping(): void
// {
//     parent::withoutWrapping();
// }

}
