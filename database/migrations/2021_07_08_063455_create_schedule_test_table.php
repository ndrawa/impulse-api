<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_tests', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('schedule_id', 26);
            $table->char('test_id', 26);
            $table->datetime('time_start');
            $table->datetime('time_end');
            $table->boolean('is_active');
            $table->string('auth');
            $table->timestamps();

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade');
            $table->foreign('schedule_id')
                ->references('id')
                ->on('schedules')
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
        //
    }
}
