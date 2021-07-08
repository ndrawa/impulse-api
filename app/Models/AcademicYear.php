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
}
