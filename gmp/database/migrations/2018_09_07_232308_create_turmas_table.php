<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modulo_id')->unsigned()->default(1);
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');      
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->String('nome');
            $table->integer('ano_lectivo');
            $table->enum('periodo',['Matinal','Vespertino','Noturno']);
            $table->integer('sala_id')->unsigned()->default(1);
            $table->foreign('sala_id')->references('id')->on('salas');
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
        Schema::dropIfExists('turmas');
    }
}
