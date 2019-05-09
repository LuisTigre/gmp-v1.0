<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atividades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');            
            $table->integer('atividadeGrupo_id')->unsigned()->default(1);
            $table->foreign('atividadeGrupo_id')->references('id')->on('atividade_grupos')->onDelete('cascade'); 
            $table->integer('epoca_id')->unsigned()->default(1);
            $table->foreign('epoca_id')->references('id')->on('epocas')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->datetime('prazo_inicial');
            $table->datetime('prazo_final');    
            $table->string('descricao');    
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
        Schema::dropIfExists('atividades');
    }
}
