<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtividadeablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atividadeables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('atividadeable_id');
            $table->string('atividadeable_type');
            $table->string('local')->nullable();
            $table->string('descricao')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
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
        Schema::dropIfExists('atividadeables');
    }
}
