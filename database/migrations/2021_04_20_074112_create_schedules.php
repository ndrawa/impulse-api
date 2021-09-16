<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name');
            $table->datetime('time_start')->nullable();
            $table->datetime('time_end')->nullable();
            $table->char('room_id', 26)->nullable();
            $table->char('class_course_id', 26);
            $table->char('module_id', 26);
            $table->char('academic_year_id', 26)->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('set null');
            $table->foreign('class_course_id')
                ->references('id')
                ->on('class_course')
                ->onDelete('cascade');
            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onDelete('set null');
            $table->foreign('module_id')
                ->references('id')
                ->on('modules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
