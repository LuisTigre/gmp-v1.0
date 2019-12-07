<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Disciplina extends Model
{  
              
   protected $fillable = ['acronimo','nome','user_id','categoria']; 

   public function user()
   {
    return $this->belongsTo('App\User');
   }   
   public function modulos()
   {
   	return $this->belongsToMany('App\Modulo')->withPivot('carga', 'duracao');
   }
   public function turmas()
   {
    return $this->belongsToMany('App\Turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function professores()
   {
    return $this->belongsToMany('App\Professor','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }


   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('disciplinas')
                       ->join('users','users.id','=',
                        'disciplinas.user_id')   
                       ->select('disciplinas.id','disciplinas.acronimo','disciplinas.nome','disciplinas.acronimo','disciplinas.categoria','users.name as usuario')                     
                       ->orderBy('disciplinas.nome','ASC')
                       ->paginate($paginate);
       }else{

       // $listaModelo = DB::table('professors')
       //                 ->join('users','users.id','=','professors.user_id')
       //                 ->select('professors.id','professors.titulo','professors.descricao','users.name','professors.data')
       //                 ->where('professors.user_id','=',$user->id)
       //                 ->orderBy('professors.id','DESC')
       //                 ->paginate($paginate);
       }
       return $listaModelo;
   }

   public function estatistica($turma_id)
   {
      $turma = Turma::find($turma_id);
      $alunos = $this->buscarAlunosDaDisciplina($turma)['data'];
       // dd($alunos);       
       $epoca = Epoca::where('Activo','S')->first();      
       $ano_lectivo = $epoca->ano_lectivo;       
       $trim = $epoca->trimestre;   
       $dados = collect([]);
       $data = collect([]);
       


       
       $listaCabecalho = ['disciplina','professor','turma','alunos_existentes','media','numero_de_posetivas','numero_de_negativas','nota_mais_baixa','nota_mais_alta','alunos_nao_avaliados','reprovados','aprovados'];
       
            
                       
            $disciplina = $this;

            $professor = Professor::find($turma->disciplinas()->where('disciplina_id',$disciplina->id)->first()->pivot->professor_id); 
            
            $lista = Turma::avaliacaoTrimestralDaTurmaParaProfessor($turma,$trim)->where('disciplina_id',$disciplina->id);
            $listaModelo = collect([]);

            foreach ($lista as $key => $aluno) {
            
              if($alunos->where('id',$aluno->aluno_id)->isNotEmpty()){
                  $listaModelo->put($key,$aluno);
              }
               
            }

            
            $total_existente = $listaModelo->isNotEmpty() ? $listaModelo->count() : $turma->alunos->count();
            
            $media = $listaModelo->median('ct');            
            $numero_de_posetivas = $listaModelo->where('ct','>=',10)->count();
            $negativas = $listaModelo->where('ct','<',10);
            $numero_de_negativas = $listaModelo->where('ct','<',10)->count();
            $negativa_mais_alta = $listaModelo->where('ct','>=',10)->sortBy('ct')->last();
            $negativa_mais_baixa = $listaModelo->where('ct','<',10)->where('ct','<>',0)->sortBy('ct')->first();
            $aprovados = $numero_de_posetivas == 0 ? 0 : round($numero_de_posetivas*100/$total_existente,2);
            $reprovados =  $numero_de_posetivas == 0 ? 0 : round($numero_de_negativas*100/$total_existente,2);

            if($listaModelo->isEmpty()){
                $alunos_sem_mac = $total_existente;
                $alunos_sem_p1 = $total_existente;
                $alunos_sem_p2 = $total_existente;
                $notas_em_falta = $alunos_sem_mac + $alunos_sem_p1;
                if($ano_lectivo < 2019){
                    $notas_em_falta = $notas_em_falta + $alunos_sem_p2;
                }
            }else{
                $alunos_sem_mac = $listaModelo->where('mac',null);
                $alunos_sem_p1 = $listaModelo->where('p1',null);
                $alunos_sem_p2 = $listaModelo->where('p2',null); 
                $notas_em_falta = $alunos_sem_mac->count() +$alunos_sem_p1->count();
                if($ano_lectivo < 2019){
                  $notas_em_falta = $notas_em_falta + $alunos_sem_p2->count();
                }              
            }
            
            $dados->put('disciplina_id',$disciplina->id);   
            $dados->put('disciplina',$disciplina);   
            $dados->put('turma_id',$turma->id);
            $dados->put('turma',$turma);
            $dados->put('professor_id',$professor->id);
            $dados->put('professor',$professor);
            $dados->put('alunos_existentes',$total_existente);
            $dados->put('media',$media);            
            $dados->put('numero_de_posetivas',$numero_de_posetivas);           
            $dados->put('numero_de_negativas',$numero_de_negativas);           
            $dados->put('negativas',$negativas);
            $dados->put('nota_mais_alta',$negativa_mais_alta);
            $dados->put('nota_mais_baixa',$negativa_mais_baixa);
            $dados->put('aprovados',$aprovados);
            $dados->put('reprovados',$reprovados);
            $dados->put('alunos_sem_mac',$alunos_sem_mac);
            $dados->put('alunos_sem_p1',$alunos_sem_p1);
            $dados->put('alunos_sem_p2',$alunos_sem_p2);
            $dados->put('notas_em_falta',$notas_em_falta);
            
            $data->put($trim,$dados);
        return $data;
   }

   public function buscarAlunosDaDisciplina($turma)
   {
    
      $user = auth()->user();      
      $data = Collect([]);
            
     
      $alunos = Turma::listaAlunos($turma->id,100);
      $alunos = $alunos->where('status','Activo');

      foreach ($alunos as $key => $aluno) {
          $aluno_objecto = Aluno::find($aluno->id);                
          $turma_anterior = $aluno_objecto->buscarTurmaAnterior($turma);
           /*SE O ALUNO NAO FOR REPETENTE*/
          if(is_null($turma_anterior)){
              $data->push($aluno);
           /*SE O ALUNO FOR REPETENTE*/
          }else{
              $modulo = Modulo::find($turma->modulo_id);
              $classe = Classe::find($modulo->classe_id);
              $avaliacaoAnual = Turma::avaliacoesDoAluno2($aluno->id,'S'); 
              $avaliacao = $avaliacaoAnual->where('disciplina_id',$this->id)->last();             
               /*SE O ALUNO NAO ESTIVER A REPETIR A DISCIPLINA*/
              if(isset($avaliacao['bloqueado_' . $classe->nome]) 
              && $avaliacao['bloqueado_' . $classe->nome] == 'S'){            
                /*SE O ALUNO ESTIVER A REPETIR A DISCIPLINA*/  
              }else{
                    $data->push($aluno);            
              }          

          }
      
      }
       
        $data2 = Collect(['data'=> $data]); 
            
      return  $data2;
      
          
     
  }
}
   

