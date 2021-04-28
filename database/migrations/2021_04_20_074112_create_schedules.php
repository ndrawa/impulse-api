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
            $table->enum('day', [
                'sunday',
                'monday',
                'tuesday',
                'wednesday',
                'thrusday',
                'friday',
                'saturday'
                ]);
            $table->time('time_start');
            $table->time('time_end');
            $table->char('room_id', 26);
            $table->time('periode_start');
            $table->time('periode_end');
            $table->char('class_id', 26);
            $table->char('module_id', 26);
            $table->timestamps();
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
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
