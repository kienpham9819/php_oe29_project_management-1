<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\PermissionTrait;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, PermissionTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'github_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('is_leader');
    }

    public function teaches()
    {
        return $this->hasMany(Course::class);
    }

    public function taskLists()
    {
        return $this->hasMany(TaskList::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, TaskList::class);
    }
}
