<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Praktikan extends BaseModel
{
    protected $table = "praktikan";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asprak_class_course_id',
        'student_id',
    ];
    
    public function asprak_class_cource()
    {
        return $this->hasMany(Asprak::class);
    }
}
