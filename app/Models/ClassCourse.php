<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassCourse extends BaseModel
{
    protected $table = "class_course";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'class_id',
        'course_id',
        'academic_year_id'
    ];

    public function classes()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function courses()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function academic_years()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
