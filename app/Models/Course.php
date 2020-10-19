<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function user()
    {
        //course - lecturer
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        // course - students
        return $this->belongsToMany(User::class);
    }
}
