<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_course', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('class_id', 26);
            $table->char('course_id', 26);
            $table->char('staff_id', 26);
            $table->char('academic_year_id', 26);
            $table->timestamps();
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->onDelete('cascade');
            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onDelete('cascade');
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
            $table->foreign('staff_id')
                ->references('id')
                ->on('staffs')
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
