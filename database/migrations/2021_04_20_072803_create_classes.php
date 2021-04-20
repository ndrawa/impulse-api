<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('staff_id', 26);
            $table->string('name');
            $table->char('course_id', 26);
            $table->string('academic_year');
            $table->string('semester');
            $table->foreign('staff_id')
                ->references('id')
                ->on('staffs')
                ->onDelete('cascade');
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
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
        Schema::dropIfExists('classes');
    }
}
