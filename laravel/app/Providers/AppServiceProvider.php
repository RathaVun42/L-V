<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping(); // this will prevent every resource to wrap data inside data
                                        // "data": [
                                        //     {
                                        //       "id": 12,
                                        //       "name": "Anna",
                                        //       "email": "anna@gmail.com"
                                        //     }
                                        //   ],
                                        // but if our data contains paginated, this function will no affect on that data (mean our resource will wrap our data inside data: [])
    }
}
