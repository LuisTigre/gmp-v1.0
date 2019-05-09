<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpocasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epocas', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('trimestre',['I','II','III'])->default('I');
            $table->integer('ano_lectivo');
            $table->date('start_time')->default(now());
            $table->date('end_time')->default(now());
            $table->date('planned_start_time')->nullable();
            $table->date('planned_end_time')->nullable();  
            $table->enum('activo',['S','N'])->default('N');
            $table->enum('fechado',['S','N'])->default('N');
            $table->integer('user_id')->unsigned()->default(1);
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
        Schema::dropIfExists('epocas');
    }
}
