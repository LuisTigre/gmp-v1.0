<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAquisicaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aquisicaos', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('aluno_id')->unsigned()->index()->default(1);
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index()->default(1);
            $table->foreign('user_id')->references('id')->on('users');  
            $table->String('documento_tipo')->nullable();            
             $table->enum('status',['Pendente','Em Processo','Concluido','Entregue'])->default('Pendente');            
            $table->String('data')->nullable();            
            $table->String('hora')->nullable();             
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
        Schema::dropIfExists('aquisicaos');
    }
}
