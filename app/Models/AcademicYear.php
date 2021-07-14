<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends BaseModel
{
    protected $table = "academic_years";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'year',
        'semester'
    ];

    public function class_course()
    {
        return $this->hasMany(ClassCourse::class, 'academic_year_id', 'id');
    }
}
