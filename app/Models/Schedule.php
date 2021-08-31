<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends BaseModel
{
    protected $table = "schedules";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'day',
        'time_start',
        'time_end',
        'room_id',
        'class_course_id',
        'module_id',
        'academic_year_id',
        'date'
    ];
    // module_id belum dimasukkan, menunggu instruksi lebih lanjut karena
    // ada relasi ke tabel yg belum dibuat

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function class_course()
    {
        return $this->belongsTo(ClassCourse::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
