<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePraktikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('praktikan', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('asprak_class_course_id', 26);
            $table->char('student_id', 26);
            $table->timestamps();
            $table->foreign('asprak_class_course_id')
                ->references('id')
                ->on('asprak_class_course')
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
        Schema::dropIfExists('praktikan');
    }
}
