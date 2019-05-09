<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->increments('id');
            $table->String('nome');
            $table->String('acronimo');                  
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('professor_id')->unsigned()->default(1);
            $table->foreign('professor_id')->references('id')->on('professors')->onDelete('cascade');
            $table->integer('area_id')->unsigned()->default(1);
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');  
            $table->String('nome_instituto_mae')->nullable();
            $table->String('director_instituto_mae')->nullable();
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cursos');
    }
}
