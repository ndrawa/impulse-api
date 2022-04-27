<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Ticket;

class TicketTransformer extends TransformerAbstract
{
    public function transform(Ticket $ticket)
    {
        return [
            'id' => $ticket->id,
            'nim' => $ticket->nim,
            'name' => $ticket->name,
            'course_name' => $ticket->course_name,
            'class_name' => $ticket->class_name,
            'practicum_day' => $ticket->practicum_day,
            'practice_session' => $ticket->practice_session,
            'username_sso' => $ticket->username_sso,
            'password_sso' => $ticket->password_sso,
            'note_student' => $ticket->note_student,
            'note_confirmation' => $ticket->note_confirmation
        ];
    }
}
