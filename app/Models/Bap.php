<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bap extends BaseModel
{
    protected $table = "bap";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'materi',
        'evaluasi',
        'jenis'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
