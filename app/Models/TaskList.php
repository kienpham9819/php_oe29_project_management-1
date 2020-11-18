<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskList extends Model
{
    use SoftDeletes;

    protected $dates = [
        'due_date',
        'start_date',
    ];

    protected $fillable = [
        'name',
        'description',
        'due_date',
        'user_id',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Task::class);
    }
}
