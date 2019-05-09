<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlunosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->increments('id');
            $table->String('nome');
            $table->date('data_de_nascimento');
            $table->String('idmatricula');
            $table->enum('repetente',['N','S'])->default('N');
            $table->String('telefone');
            $table->String('encarregado_tel');
            $table->String('email')->nullable();
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');        
            $table->String('photo')->nullable();
            $table->String('idade')->nullable();            
            $table->integer('modulo_id')->unsigned()->nullable();
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');   
            $table->enum('sexo',['M','F']);
            $table->enum('status',['Activo','Desistido','Suspenso','Transferido'])->default('Activo');
            $table->String('pai');
            $table->String('mae');
            $table->String('doctipo');
            $table->String('doc_numero');
            $table->String('doc_local_emissao');
            $table->String('doc_data_emissao');
            $table->String('doc_data_validade');
            $table->String('naturalidade');
            $table->String('provincia');
            $table->String('pais');
            $table->String('morada');
            $table->String('escola_origem');            
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
        Schema::dropIfExists('alunos');
    }
}
