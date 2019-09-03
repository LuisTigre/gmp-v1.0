<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Turma;

class Aluno extends Model
{  
              
   protected $fillable = ['nome','data_de_nascimento','idmatricula','repetente','sexo','telefone','encarregado_tel','email','modulo_id','status','pai','mae','doctipo','doc_numero','doc_local_emissao','doc_data_emissao','doc_data_validade','naturalidade','provincia','pais','morada','escola_origem','devedor'];
         
   // protected $fillable = ['nome','data_de_nascimento','idmatricula'];   

   public function user()
   {
   	return $this->belongsTo('App\user');
   }
   public function turmas()
   {
    return $this->belongsToMany('App\turma')->withPivot('numero','cargo','repetente','provenienca','status');
   }

   public function avaliacaos()
   {
    return $this->hasMany('App\avaliacao');
   }

   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina')->withPivot('turma_id','repetente');
   }
   
   public static function listaAlunos($paginate)
   {
    
       
           $listaAlunos = DB::table('alunos')
                        ->join('modulos','modulos.id','=','alunos.modulo_id')            
                        ->leftjoin('users','users.id','=','alunos.user_id')            
                        ->select('alunos.id','alunos.nome',
                        'alunos.data_de_nascimento',
                        'alunos.idmatricula','modulos.nome as curso',
                        'alunos.sexo','alunos.status','users.name as usuario')            
                        ->orderBy('alunos.nome','ASC')
                        ->paginate($paginate);
                      
       
       return $listaAlunos;
   }
   


   public static function exportarAlunos()
   {
       $user = auth()->user();
       if($user->admin == "S"){

       }
           
   }

  public function migrarNotasAnteriores(){    
               
      $user = auth()->user();      
      $turmas = $this->turmas()->get();     
      $turma_actual = $this->turmas()->get()->sortBy('ano_lectivo')->last(); 
      $turma_anterior_da_classe_actual = $this->buscarTurmaAnterior($turma_actual); 
      /*SE O ALUNO FOR REPETENTE*/
      if(!is_null($turma_anterior_da_classe_actual)){              

          $avaliacaoAnual = Turma::avaliacoesDoAluno2($this->id,'S');
          $modulo = Modulo::find($turma_actual->modulo_id);
          $modulo_nome = explode(" ", $modulo->nome);                 
          $mn = $modulo_nome[1];     

                    $discsss= Collect([]);
                    $moduloss= Collect([]);

          foreach ($avaliacaoAnual as $avaliacao) {
            /*SE A COTACAO ANUAL RESULTA EM APROVACAO*/
            
            if(isset($avaliacao['result']) 
            && ($avaliacao['result'] == 'Trans.' 
            || $avaliacao['result'] == 'Continua')){     
              
                  $moduloss->push($avaliacao['avaliacao_id_' . $mn]);   
                 if($avaliacao['avaliacao_id_' . $mn] != ''){
                     $discsss->push($avaliacao);   
                      $avaliacaoNew = Avaliacao::find($avaliacao['avaliacao_id_' . $mn]);
                      if(!is_null($avaliacaoNew)){
                        $newAvaliacao = new Avaliacao([         
                          'professor_id' => $avaliacaoNew->professor_id,
                          'user_id' => $turma_actual->user_id,
                          'turma_id' => $turma_actual->id,
                          'disciplina_id' => $avaliacaoNew->disciplina_id,
                          'aluno_id' => $avaliacaoNew->aluno_id,
                          'mac1' => $avaliacaoNew['mac1'],
                          'p11' => $avaliacaoNew['p11'],
                          'p12' => $avaliacaoNew['p12'],                                     
                          'mac2' => $avaliacaoNew['mac2'],
                          'p21' => $avaliacaoNew['p21'],
                          'p22' => $avaliacaoNew['p22'],                                     
                          'mac3' => $avaliacaoNew['mac3'],
                          'p31' => $avaliacaoNew['p31'],
                          'exame1' => $avaliacaoNew['exame1'],                                    
                          'exame2' => $avaliacaoNew['exame2'],                                    
                          'p32' => $avaliacaoNew['p32'],                                    
                          'status' => 'bloqueado'                                    
                       ]);                          
                          $newAvaliacao->save();                         
                     }     
                 }     
             
          
          }
        }
        
            $this->anularNotasAnteriores($turma_anterior_da_classe_actual,$turma_actual);

       /*SE O ALUNO NAO FOR REPETENTE*/
      }else{
          $mn = explode(' ', $turma_actual->nome);
          $mn_arr = explode('ª', $mn[1]);         
          $classe_ant = $mn_arr[0]-1 . 'ª';

          /*SE A CLASSE FOR DIFERENTE DA 10 CLASSE*/
          if($mn_arr[0] > 10){
              $modulo_da_classe_anterior = Modulo::where('nome',  $mn[0] . ' ' . $classe_ant)->get()->last();
              // dd($modulo_da_classe_anterior); 

              $avaliacaoAnual = Turma::avaliacoesDoAluno2($this->id,'S');
              $recursos = $avaliacaoAnual->where('result','exame2');
              
              foreach ($recursos as $recurso){    
                $avaliacao = Avaliacao::where('disciplina_id',$recurso['disciplina_id'])->get()->where('aluno_id',
                $this->id)->last();            
                $avaliacao->update(['exame1'=>null]);                      
              } 
          }              
             
      }
            
  }

   public function buscarTurmaAnterior($turma_actual){
          $turmas = $this->turmas()->get();
          $turma_anterior = $turmas->where('ano_lectivo','<',$turma_actual->ano_lectivo)->where('modulo_id',
            $turma_actual->modulo_id)->last();
          return $turma_anterior;
   }

   public function buscarTurmaDaClasseAnterior($turma_actual)
   {

          $modulo = $turma_actual->modulo->moduloAnterior();
          $turmas = $this->turmas()->get();          
          $turma_anterior = $turmas->where('ano_lectivo','<',$turma_actual->ano_lectivo)->where('modulo_id',
            $modulo->id)->last();
          return $turma_anterior;
   }
   public function buscarTurmaDaClasse($turma_actual,$classe)
   {
          
          $modulo = $turma_actual->modulo;
          $curso = $modulo->curso;          
          $modulo_anterior = Modulo::all()->where('nome',$curso->acronimo . ' ' . $classe)->first();          
          $turmas = $this->turmas()->get();          
          $turma_anterior = $turmas->where('ano_lectivo','<',$turma_actual->ano_lectivo)->where('modulo_id',
            $modulo_anterior->id)->last();
          return $turma_anterior;
   }
   public function buscarTurmaActualDaClasse($turma_actual)
   {         
          $modulo = $turma_actual->modulo;
          $curso = $modulo->curso;          
          $modulo_anterior = $modulo->moduloAnterior(); 
          $turmas = $this->turmas()->get();                  
          // if($this->id == 445){
          //   dd($turmas);
          // }  
          $turma_actual = $turmas->where('modulo_id',$modulo_anterior->id)->where('ano_lectivo',$turma_actual->ano_lectivo)->last();
          return $turma_actual;
   }
   
   

   public function anularNotasAnteriores($turma,$turma_actual){ 
      $avaliacoes = $this->avaliacaos()->where('turma_id',$turma->id)->get();
      foreach ($avaliacoes as $avaliacao) {
        if(round($avaliacao->exame1,1) >= 10 || round($avaliacao->exame2,1)){
            $avaliacao->update(['status'=>'anulado']);
        }
      }                  
      // $this->avaliacaos()->where('turma_id',$turma->id)->update(['status'=>'anulado']);
      $this->turmas()->updateExistingPivot($turma_actual->id,
        [
         'aluno_id'=>$this->id,
         'turma_id'=>$turma_actual->id,
         'repetente'=>'S'
        ]);     

   }

   public function revalidarNotasAnteriores($turma){ 
      if(!is_null($turma)){
        $this->avaliacaos()->where('turma_id',$turma->id)->update(['status'=>null]);
      }            
      
   }
   
}
