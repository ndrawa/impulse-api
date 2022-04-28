<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends BaseModel
{
    protected $table = "tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nim',
        'name',
        'course_name',
        'class_name',
        'practicum_day',
        'practice_session',
        'username_sso',
        'password_sso',
        'note_student',
        'note_confirmation'
    ];
}
