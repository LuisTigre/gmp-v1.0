<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstituicaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instituicaos', function (Blueprint $table) {
            $table->increments('id');
            $table->String('nome');      
            $table->String('sigla');      
            $table->String('logo');      
            $table->String('lema');      
            $table->String('numero');      
            $table->String('email');      
            $table->String('telefone1');      
            $table->String('telefone2');      
            $table->String('telefone3');      
            $table->String('director_instituicao');      
            $table->enum('director_instituicao_sexo',['M','F'])->default('M');
            $table->String('director_pedagogico');
            $table->enum('director_pedagogico_sexo',['M','F'])->default('M');      
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('instituicaos');
    }
}
