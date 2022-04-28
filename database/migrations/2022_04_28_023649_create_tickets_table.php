<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->char('id', 26);
            $table->string('nim');
            $table->string('name');
            $table->string('course_name');
            $table->string('class_name');
            $table->enum('practicum_day', [
                'sunday',
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday'
            ]);
            $table->enum('practice_session', [
                '06.30 - 09.30',
                '07.30 - 10.30',
                '08.30 - 11.30',
                '09.30 - 12.30',
                '10.30 - 13.30',
                '11.30 - 14.30',
                '12.30 - 15.30',
                '13.30 - 16.30',
                '14.30 - 17.30',
                '15.30 - 18.30',
                '16.30 - 19.30',
                '17.30 - 20.30',
                '18.30 - 21.30'
            ]);
            $table->string('username_sso');
            $table->string('password_sso');
            $table->string('note_student');
            $table->string('note_confirmation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
