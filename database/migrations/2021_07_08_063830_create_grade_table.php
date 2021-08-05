<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('asprak_id', 26);
            $table->char('schedule_test_id', 26);
            $table->integer('grade_tp');
            $table->integer('grade_pretest');
            $table->integer('grade_jurnal');
            $table->integer('grade_posttest');
            $table->integer('grade');
            $table->timestamps();

            $table->foreign('asprak_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('schedule_test_id')
                ->references('id')
                ->on('schedule_tests')
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
        Schema::dropIfExists('grade');
    }
}
