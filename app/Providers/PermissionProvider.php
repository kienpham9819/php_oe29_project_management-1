<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Permission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Exception;

class PermissionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::before( function ($user,$ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
        try {
            Permission::all()->map(function ($permission, $course_id = null) {
                Gate::define($permission->slug, function ($user) use ($permission, $course_id)
                {
                    return $user->hasPermissionTo($permission, $course_id);
                });
            });
        } catch (Exception $e) {
            return false;
        }
        Gate::define('edit-comment', function ($user, $comment) {
            return $user->id === $comment->user_id;
        });

        Gate::define('delete-comment', function ($user, $comment) {
            return $user->id === $comment->user_id;
        });
    }
}
