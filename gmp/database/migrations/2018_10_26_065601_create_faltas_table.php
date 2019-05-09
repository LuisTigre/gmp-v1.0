<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaltasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faltas', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('atividade_id')->unsigned()->default(1);
            $table->foreign('atividade_id')->references('id')->on('atividades')->onDelete('cascade'); 
            $table->integer('faltable_id');
            $table->string('faltable_type');
            $table->enum('justificado',['S','N'])->default('N');
            $table->string('descricao')->nullable();
            $table->string('anexo')->nullable();
            $table->datetime('data_hora');
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
        Schema::dropIfExists('faltas');
    }
}
