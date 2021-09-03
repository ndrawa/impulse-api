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
            $table->char('student_id', 26);
            $table->char('schedule_test_id', 26);
            $table->char('question_id', 26);
            $table->float('grade');
            $table->char('asprak_id', 26)->nullable();
            $table->timestamps();

            $table->foreign('asprak_id')
                ->references('id')
                ->on('students')
                ->onDelete('set null');
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('question_id')
                ->references('id')
                ->on('students')
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
