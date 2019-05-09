<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlunoDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aluno_disciplina', function (Blueprint $table) {
            $table->integer('turma_id')->unsigned()->index()->default(1);
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('cascade');      
            $table->integer('disciplina_id')->unsigned()->index()->default(1);
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');  
            $table->integer('aluno_id')->unsigned()->index()->nullable();
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index()->default(1);
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
        Schema::dropIfExists('aluno_disciplina');
    }
}
