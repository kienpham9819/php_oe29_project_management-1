<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Task\TaskRepositoryInterface;
use App\Repositories\Task\TaskRepository;
use App\Repositories\TaskList\TaskListRepositoryInterface;
use App\Repositories\TaskList\TaskListRepository;
use App\Repositories\Attachment\AttachmentRepositoryInterface;
use App\Repositories\Attachment\AttachmentRepository;
use App\Repositories\Comment\CommentRepositoryInterface;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\Group\GroupRepository;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Project\ProjectRepository;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            TaskRepositoryInterface::class,
            TaskRepository::class,
        );
        $this->app->singleton(
            TaskListRepositoryInterface::class,
            TaskListRepository::class,
        );
        $this->app->singleton(
            AttachmentRepositoryInterface::class,
            AttachmentRepository::class,
        );
        $this->app->singleton(
            CommentRepositoryInterface::class,
            CommentRepository::class,
        );
        $this->app->singleton(
            RoleRepositoryInterface::class,
            RoleRepository::class,
        );
        $this->app->singleton(
            CourseRepositoryInterface::class,
            CourseRepository::class,
        );
        $this->app->singleton(
            UserRepositoryInterface::class,
            UserRepository::class,
        );
        $this->app->singleton(
            GroupRepositoryInterface::class,
            GroupRepository::class,
        );
        $this->app->singleton(
            ProjectRepositoryInterface::class,
            ProjectRepository::class,
        );
        $this->app->singleton(
            PermissionRepositoryInterface::class,
            PermissionRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
