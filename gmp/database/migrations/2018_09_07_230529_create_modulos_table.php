<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->increments('id');
            $table->String('nome');           
            $table->integer('curso_id')->unsigned()->default(1);
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');           
            $table->integer('classe_id')->unsigned()->default(1);
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');      
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');           
            $table->String('ano');           
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
        Schema::dropIfExists('modulos');
    }
}
