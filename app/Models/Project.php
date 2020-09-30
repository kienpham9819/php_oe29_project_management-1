<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function taskLists()
    {
        return $this->hasMany(List::class);
    }
}
