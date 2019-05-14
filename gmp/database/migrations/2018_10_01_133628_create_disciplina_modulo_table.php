<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplinaModuloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disciplina_modulo', function (Blueprint $table) {

            $table->integer('modulo_id')->unsigned()->index()->default(1);
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');      
            $table->integer('disciplina_id')->unsigned()->index()->default(1);
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade'); 
            $table->integer('user_id')->unsigned()->index()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('carga');            
            $table->enum('terminal',['N','S'])->default('N');
            $table->enum('do_curso',['N','S'])->default('N');
            $table->enum('curricular',['N','S'])->default('S');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disciplina_modulo');
    }
}
