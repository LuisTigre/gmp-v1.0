<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplinaTurmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disciplina_turma', function (Blueprint $table) {
            $table->integer('turma_id')->unsigned()->index()->default(1);
            $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('cascade');      
            $table->integer('disciplina_id')->unsigned()->index()->default(1);
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');  
            $table->integer('professor_id')->unsigned()->index()->nullable();
            $table->foreign('professor_id')->references('id')->on('professors')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->enum('director',['N','S'])->default('N');
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
        Schema::dropIfExists('disciplina_turma');
    }
}
