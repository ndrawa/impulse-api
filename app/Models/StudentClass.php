<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClass extends BaseModel
{
    protected $table = "students_classes";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // protected $fillable = [
    // ];

    public function students()
    {
        return $this->belongsTo(Student::class);
    }

    public function classes()
    {
        return $this->belongsTo(Classroom::class);
    }
}
