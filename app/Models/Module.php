<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends BaseModel
{
    protected $table = "modules";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'pretest_id',
        'posttest_id',
        'journal_id',
        'index',
        'academic_year_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function pretest()
    {
        return $this->belongsTo(Test::class, 'pretest_id');
    }

    public function posttest()
    {
        return $this->belongsTo(Test::class, 'posttest_id');
    }

    public function journal()
    {
        return $this->belongsTo(Test::class, 'journal_id');
    }

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
