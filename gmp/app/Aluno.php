<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Turma;

class Aluno extends Model
{  
              
   protected $fillable = ['nome','data_de_nascimento','idmatricula','repetente','sexo','telefone','encarregado_tel','email','modulo_id','status','pai','mae','doctipo','doc_numero','doc_local_emissao','doc_data_emissao','doc_data_validade','naturalidade','provincia','pais','morada','escola_origem'];
         
   // protected $fillable = ['nome','data_de_nascimento','idmatricula'];   

   public function user()
   {
   	return $this->belongsTo('App\user');
   }
   public function turmas()
   {
    return $this->belongsToMany('App\turma')->withPivot('numero','cargo','repetente','status');
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
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaAlunos = DB::table('alunos')
                        ->join('modulos','modulos.id','=','alunos.modulo_id')            
                        ->leftjoin('users','users.id','=','alunos.user_id')            
                        ->select('alunos.id','alunos.nome',
                        'alunos.data_de_nascimento',
                        'alunos.idmatricula','modulos.nome as curso',
                        'alunos.sexo','alunos.status','users.name as usuario')            
                        ->orderBy('alunos.nome','ASC')
                        ->paginate($paginate);
                      
       }else{

       $listaAlunos = DB::table('alunos')
                       ->join('users','users.id','=','alunos.user_id')
                       ->select('alunos.id','alunos.titulo','alunos.descricao','users.name','alunos.data')  
                       ->where('alunos.user_id','=',$user->id)
                       ->orderBy('alunos.id','DESC')
                       ->paginate($paginate);
       }
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
      $turma_actual = $this->turmas()->get()->last(); 
      $turma_anterior_da_classe_actual = $turmas->where('ano_lectivo','<',$turma_actual->ano_lectivo)->where('modulo_id',
      $turma_actual->modulo_id);
            
      
      if($turma_anterior_da_classe_actual->isNotEmpty()){
          $avaliacaoAnual = Turma::avaliacoesDoAluno2($this->id,'S');

          foreach ($avaliacaoAnual as $avaliacao) {
            if(isset($avaliacao['result']) 
            && ($avaliacao['result'] == 'Tran.' 
            || $avaliacao['result'] == 'Continua')){

              $modulos = ['10ª','11ª','12ª','13ª'];

                foreach ($modulos as $mn) {
                 if($avaliacao['avaliacao_id_' . $mn] != ''){
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
                          'p32' => $avaliacaoNew['p32']                                    
                      ]);                          
                          $newAvaliacao->save();                         
                  }     
              }     
            }     
          }else{
                  // $avaliacao = $avaliacao;                                    
                  // if(!is_null($avaliacao) 
                  //   && isset($avaliacao['disciplina_id'])){                                      
                  //   $this->disciplinas()->firstOrCreate([                    
                  //   'turma_id' => $turma_actual->id,
                  //   'aluno_id' => $this->id,
                  //   'disciplina_id' => $avaliacao['disciplina_id']                   
                  // ]);     
                  // }
                    
                          

          }
        }
            $this->anularNotasAnteriores($turma_anterior_da_classe_actual);
      }else{

          $mn = explode(' ', $turma_actual->nome);
          $mn_arr = explode('ª', $mn[1]);         
          $classe_ant = $mn_arr[0]-1 . 'ª';
          
          $modulo_da_classe_anterior = Modulo::where('nome',  $mn[0] . ' ' . $classe_ant)->get()->last();
          // $turma_da_classe_anterior = $turmas->where('modulo_id',$modulo_da_classe_anterior->id)->last(); 
          $avaliacaoAnual = Turma::avaliacoesDoAluno2($this->id,'S');
          $recursos = $avaliacaoAnual->where('result','exame2');
          
          foreach ($recursos as $recurso){    
            $avaliacao = Avaliacao::where('disciplina_id',$recurso['disciplina_id'])->get()->where('aluno_id',
            $this->id)->last();            
            $avaliacao->update(['exame1'=>null]);                      
          }      
             
      }
            
  }

   public function anularNotasAnteriores($turma){
      $this->avaliacaos()->where('turma_id',$turma->id)->update(['status'=>'anulado']); 
      $turma->alunos()->updateExistingPivot($this->id,
        [
         'aluno_id'=>$this->id,
         'turma_id'=>$turma->id,
         'repetente'=>'S'
        ]);          
           

   }
   
}
