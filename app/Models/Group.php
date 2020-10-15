<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'course_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('is_leader');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
