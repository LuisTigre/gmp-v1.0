<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAvaliacaosView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::raw("
      CREATE VIEW avaliacaos_view AS
      (     
            select av.id, 
            av.professor_id, 
            av.user_id, 
            av.disciplina_id, 
            av.turma_id, 
            av.aluno_id, 
            av.mac1, 
            av.p11, 
            av.p12,
            av.fnj1, 
            av.fj1, 
            av.mac2, 
            av.p21, 
            av.p22, 
            av.fnj2, 
            av.fj2, 
            av.mac3, 
            av.p31, 
            av.p32, 
            av.fnj3, 
            av.fj3,            
            year(now())-year(al.data_de_nascimento) as idade,
            round((av.mac1 + av.p11 + av.p12)/3,1) as ct1a,
            round((av.mac1 + av.p11)/2,1) as ct1b,
            round(((av.mac1 + av.p11 + av.p12)/3 + (av.mac2 + av.p21 + av.p22)/3)/2,1) as ct2a,
            round(((av.mac1 + av.p11)/2 + (av.mac2 + av.p21)/2)/2,1) as ct2b,
            round((((av.mac1 + av.p11 + av.p12)/3 + (av.mac2 + av.p21 + av.p22)/3)/2 + (av.mac3 + av.p31)/2)/2,1) as ct3a,
            round((((av.mac1 + av.p11)/2 + (av.mac2 + av.p21)/2)/2 + (av.mac3 + av.p31)/2)/2,1) as ct3b,
            round((((av.mac1 + av.p11 + av.p12)/3 + (av.mac2 + av.p21 + av.p22)/3)/2 + (av.mac3 + av.p31)/2)/2,1) as mtca,
            round((((av.mac1 + av.p11)/2 + (av.mac2 + av.p21)/2)/2 + (av.mac3 + av.p31)/2)/2,1) as mtcb,
            round((((av.mac1 + av.p11 + av.p12)/3 + (av.mac2 + av.p21 + av.p22)/3)/2 + (av.mac3 + av.p31)/2)/2 * 0.6,1) as sessentaa,
            round((((av.mac1 + av.p11)/2 + (av.mac2 + av.p21)/2)/2 + (av.mac3 + av.p31)/2)/2 * 0.6,1) as sessentab,
            round(av.p32 * 0.4,1) as quarenta,
            round((((av.mac1 + av.p11 + av.p12)/3 + (av.mac2 + av.p21 + av.p22)/3)/2 + (av.mac3 + av.p31)/2)/2 * 0.6 + av.p32 * 0.4,1) as notafinala,
            round((((av.mac1 + av.p11)/2 + (av.mac2 + av.p21)/2)/2 + (av.mac3 + av.p31)/2)/2 * 0.6 + av.p32 * 0.4,1) as notafinalb,
            round(av.exame1,1) as exame1,
            round(av.exame2,1) as exame2,
            round(av.exame3,1) as exame3,
            av.status avaliacao_status, 
            av.created_at, 
            av.updated_at,
            atu.numero,
            al.status as aluno_status, 
            al.nome as aluno, 
            al.idmatricula as idmatricula,
            tur.modulo_id,
            tur.nome as turma, 
            tur.ano_lectivo,
            disc.nome as disciplina, 
            disc.acronimo, 
            disc.categoria,
            us.name as usuario
            from avaliacaos av 
            inner join alunos al on av.aluno_id = al.id 
            inner join turmas tur on av.turma_id  = tur.id
            inner join disciplinas disc on av.disciplina_id = disc.id
            inner join professors prof on av.professor_id = prof.id
            inner join aluno_turma atu on av.aluno_id = atu.aluno_id
            left  join users us on av.user_id = us.id
            group by av.id;          
      )

      ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
      {
        DB::statement('DROP VIEW IF EXISTS avaliacaos_view');
      }  
}
