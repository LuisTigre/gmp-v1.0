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
    return $this->belongsTo('App\user');
   }   
   public function modulos()
   {
   	return $this->belongsToMany('App\modulo')->withPivot('carga', 'duracao');
   }
   public function turmas()
   {
    return $this->belongsToMany('App\turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function professores()
   {
    return $this->belongsToMany('App\professor','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
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


   public function buscarAlunosDaDisciplina($turma)
   {
    
      $user = auth()->user();      
      $data = Collect([]);
            
      /*SE O ALUNO FOR REPETENTE*/
  $alunos = Turma::listaAlunos($turma->id,100);
      foreach ($alunos as $key => $aluno) {
          $aluno_objecto = Aluno::find($aluno->id);
          $turmas = $aluno_objecto->turmas()->get();         
          $turma_anterior = $aluno_objecto->buscarTurmaAnterior($turma);
          if(is_null($turma_anterior)){
              $data->push($aluno);
          }else{
              $modulo = Modulo::find($turma->modulo_id);
              $classe = Classe::find($modulo->classe_id);
              $avaliacaoAnual = Turma::avaliacoesDoAluno2($aluno->id,'S'); 
              $avaliacao = $avaliacaoAnual->where('disciplina_id',$this->id)->last();             
              
              if(isset($avaliacao['bloqueado_' . $classe->nome]) 
              && $avaliacao['bloqueado_' . $classe->nome] == 'S'){            
                  
              }else{
                    $data->push($aluno);            
              }          

          }
      
      }
       
        $data2 = Collect(['data'=> $data]);       
      return  $data2;
      
          
     
  }
}
   

