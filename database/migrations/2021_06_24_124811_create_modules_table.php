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
            $table->char('pretest_id', 26);
            $table->char('posttest_id', 26);
            $table->char('journal_id', 26);
            $table->timestamps();

            $table->foreign('pretest_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade')
                ->nullable();
            $table->foreign('posttest_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade')
                ->nullable();
            $table->foreign('journal_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade')
                ->nullable();
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
