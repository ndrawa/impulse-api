<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsprakPresence extends Model
{
    protected $table = "asprak_presence";

    protected $fillable = [
        'student_id',
        'schedule_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
