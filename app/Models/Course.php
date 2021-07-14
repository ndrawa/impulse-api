<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends BaseModel
{
    protected $table = "courses";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code'
    ];

    public function class_course()
    {
        return $this->hasMany(ClassCourse::class, 'course_id', 'id');
    }
}
