<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfessorTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('professor',['N','S'])->default('N');
            $table->enum('director_turma',['N','S'])->default('N');
            $table->enum('coordenador_curso',['N','S'])->default('N');
            $table->enum('secretario_pedagogico',['N','S'])->default('N');
            $table->enum('secretario_geral',['N','S'])->default('N');
            $table->enum('pedagogico',['N','S'])->default('N');
            $table->enum('director',['N','S'])->default('N');
            $table->enum('master',['N','S'])->default('N');
            $table->enum('aluno',['N','S'])->default('N');
            $table->enum('funcionario',['N','S'])->default('N');
            $table->enum('encarregado',['N','S'])->default('N');
            $table->enum('contabilista',['N','S'])->default('N');
            $table->enum('ceo',['N','S'])->default('N');
            $table->enum('activo',['N','S'])->default('S');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('professor');
            $table->dropColumn('director_turma');
            $table->dropColumn('coordenador_curso');
            $table->dropColumn('activo');
        });
    }
}
