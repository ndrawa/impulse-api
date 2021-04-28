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
        'name',
        'academic_year',
        'semester'
    ];

    public function staffs()
    {
        return $this->belongsTo(Staff::class);
    }

    public function courses()
    {
        return $this->belongsTo(Course::class);
    }
}
