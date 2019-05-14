<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->increments('id');
            $table->String('nome');
            $table->enum('laboratorio',['S','N'])->default('N');
            $table->String('descricao')->nullable();             
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('turma_id')->unsigned()->default(1);
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('cascade');
            $table->integer('disciplina_id')->unsigned()->default(1);
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->integer('sala_id')->unsigned()->default(1);
            $table->foreign('sala_id')->references('id')->on('salas')->onDelete('cascade');
            $table->integer('tempo_id')->unsigned()->nullable();
            $table->foreign('tempo_id')->references('id')->on('tempos')->onDelete('cascade');
            $table->integer('professor_id')->unsigned()->nullable();
            $table->foreign('professor_id')->references('id')->on('professors')->onDelete('cascade');
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
        Schema::dropIfExists('aulas');
    }
}
