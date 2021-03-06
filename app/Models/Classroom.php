<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends BaseModel
{
    protected $table = "classes";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function staffs()
    {
        return $this->belongsTo(Staff::class);
    }

    public function class_course()
    {
        return $this->hasMany(ClassCourse::class, 'class_id', 'id');
    }
}
