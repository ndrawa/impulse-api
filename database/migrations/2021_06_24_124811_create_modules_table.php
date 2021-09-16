<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('course_id', 26);
            $table->char('pretest_id', 26)->nullable();
            $table->char('posttest_id', 26)->nullable();
            $table->char('journal_id', 26)->nullable();
            $table->integer('index');
            $table->char('academic_year_id', 26)->nullable();
            $table->timestamps();

            $table->foreign('pretest_id')
                ->references('id')
                ->on('tests')
                ->onDelete('set null');
            $table->foreign('posttest_id')
                ->references('id')
                ->on('tests')
                ->onDelete('set null');
            $table->foreign('journal_id')
                ->references('id')
                ->on('tests')
                ->onDelete('set null');
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
