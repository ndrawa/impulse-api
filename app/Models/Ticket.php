<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = "tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
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
