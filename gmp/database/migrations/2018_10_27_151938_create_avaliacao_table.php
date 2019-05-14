<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvaliacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('professor_id')->unsigned()->default(1);
            $table->foreign('professor_id')->references('id')->on('professors')->onDelete('cascade'); 
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('disciplina_id')->unsigned()->default(1);
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('cascade');
            $table->integer('turma_id')->unsigned()->default(1);
            $table->integer('aluno_id')->unsigned()->default(1);
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');          
            $table->double('mac1')->nullable();
            $table->double('p11')->nullable();
            $table->double('p12')->nullable();
            $table->integer('fnj1')->nullable();
            $table->integer('fj1')->nullable();
            $table->double('mac2')->nullable();
            $table->double('p21')->nullable();
            $table->double('p22')->nullable();
            $table->integer('fnj2')->nullable();
            $table->integer('fj2')->nullable();
            $table->double('mac3')->nullable();
            $table->double('p31')->nullable();
            $table->double('p32')->nullable();
            $table->integer('fnj3')->nullable();
            $table->integer('fj3')->nullable();
            $table->double('exame1')->nullable();
            $table->double('exame2')->nullable();            
            $table->double('exame3')->nullable();            
            $table->String('status')->nullable();            
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
        Schema::dropIfExists('avaliacaos');
    }
}
