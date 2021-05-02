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
        'periode_start',
        'periode_end',
        'type',
        'room_id',
        'class_id',
        'module_id',
    ];
    // module_id belum dimasukkan, menunggu instruksi lebih lanjut karena
    // ada relasi ke tabel yg belum dibuat

    public function rooms()
    {
        return $this->belongsTo(Room::class);
    }

    public function classes()
    {
        return $this->belongsTo(Classroom::class);
    }
}
