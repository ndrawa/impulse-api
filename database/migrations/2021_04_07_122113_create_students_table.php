<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('user_id', 26);
            $table->string('nim')->unique();
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->enum('religion', [
                'islam', 
                'protestan', 
                'katolik', 
                'buddha', 
                'hindu', 
                'khonghucu',
                'kristen'
                ]);
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('students');
    }
}
