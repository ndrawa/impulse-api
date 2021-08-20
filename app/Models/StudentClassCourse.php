<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassCourse extends BaseModel
{
    protected $table = "students_class_course";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'student_id',
        'class_course_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class_course()
    {
        return $this->belongsTo(ClassCourse::class);
    }
}
