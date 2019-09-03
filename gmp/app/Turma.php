<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Professor;
use App\Aluno;
use App\Turma;
use App\Modulo;
use App\Classe;
use App\Epoca;


class Turma extends Model
{   
              
   protected $fillable = ['nome','periodo','user_id','modulo_id','ano_lectivo','numero','cargo','repetente','sala_id','provenienca'];   
   

   public function user()
   {
   	return $this->belongsTo('App\user');
   }
   public function alunos()
   {
    return $this->belongsToMany('App\aluno')->withPivot('numero','cargo','repetente','provenienca','status');
   }

   public function professores()
   {
    return $this->belongsToMany('App\professor','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina')->withPivot('director','disciplina_id','professor_id')->withTimestamps();
   }

   public function curso()
   {
    return $this->belongsTo('App\curso');
   }
   public function classe()
   {
    return $this->belongsTo('App\classe');
   }
   public function modulo()
   {
    return $this->belongsTo('App\modulo');
   }


    public function avaliacaos()
   {
    return $this->hasMany('App\avaliacao');
   }
   public function aulas()
   {
    return $this->hasMany('App\aula');
   }
    
    public static function classificaoTrimestrais2($turma_id,$trimestre){

        $turma = Turma::find($turma_id);
        $avaliacoesTrimestrais = Turma::avaliacaoTrimestralDaTurma($turma->id,$trimestre);        
        $alunos = Turma::listaAlunos2($turma_id,100);          
        $discQtd = $turma->disciplinas()->count();        
        $disciplinas = Turma::listaDisciplinas($turma_id,100);                
        $data = collect([]);   
        $data2 = collect([]);
        $discs = collect([]);
        $avaliacao = collect([]);
        $prs_info = [];                
        $newdata = collect([]);
        $i = 0;             
          foreach ($alunos as $aluno){

              $medias = Collect([]);
              $avalicaoDoAluno = $avaliacoesTrimestrais->where('aluno_id',$aluno->id);
              foreach ($disciplinas as $disciplina) {        

                  
                  $data = $avalicaoDoAluno->where('disciplina_id',$disciplina->id)->first();      
                                 
                  if(!is_null($data)){                                   
                      if($trimestre == 'I'){
                        
                        /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                        
                        $fnj1 = is_null($data->fnj1) == true? '': $data->fnj1;               
                        $fj1 = is_null($data->fj1) == true? '': $data->fj1;               
                        $ct1 = is_null($data->ct1b) == true? '': $data->ct1b;               
                        
                       /*FIM DA VERIFICAÇÃO*/

                        $data2->put($disciplina->id .'_'. 'fnj1',$fnj1);   
                        $data2->put($disciplina->id .'_'. 'fj1',$fj1);     
                        $data2->put($disciplina->id .'_'. 'ct1',$ct1);

                        $medias->push($ct1);

                      }else if($trimestre == 'II'){

                        /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                        
                        $fnj2 = is_null($data->fnj2) == true? '': $data->fnj2;               
                        $fj2 = is_null($data->fj2) == true? '': $data->fj2;               
                        $ct2 = is_null($data->ct2b) == true? '': $data->ct2b; 
                       /*FIM DA VERIFICAÇÃO*/

                        $data2->put($disciplina->id .'_'. 'fnj2',$fnj2);   
                        $data2->put($disciplina->id .'_'. 'fj2',$fj2);     
                        $data2->put($disciplina->id .'_'. 'ct2',$ct2);   
                        
                        $medias->push($ct2);
                      }else{
                        /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                        
                        $fnj3 = is_null($data->fnj3) == true? '': $data->fnj3;               
                        $fj3 = is_null($data->fj3) == true? '': $data->fj3;               
                        $notafinal = is_null($data->notafinalb) == true? '': $data->notafinalb; 
                       /*FIM DA VERIFICAÇÃO*/

                        $data2->put($disciplina->id .'_'. 'fnj3',$fnj3);   
                        $data2->put($disciplina->id .'_'. 'fj3',$fj3);     
                        $data2->put($disciplina->id .'_'. 'ct3',$notafinal);    
                        
                        $medias->push($notafinal);
                      }

                  }else{                    
                    $trim = '';
                    switch ($trimestre) {
                      case 'II':
                        $trim = '2';
                        break;
                      case 'II':
                        $trim = '3';
                        break;                      
                      default:
                        $trim = '1';
                        break;
                    }
                    
                     $ct = 'ct' . $trim;                    
                     $fnj = 'fnj' . $trim;                    
                     $fj = 'fj' . $trim;                    
                    

                    $data2->put($disciplina->id .'_'. $fnj,'');               
                    $data2->put($disciplina->id .'_'. $fj,'');             
                    $data2->put($disciplina->id .'_'. $ct,'');                    
                    
                  }
                   $i++;
                  

                  }

                    $idade = explode('-',$aluno->idade);

                    /*PREENCHIMENTO DOS DADOS PESSOAIS DO ALUNO*/
                    $data2->prepend($idade[0],'idade');                    
                    $data2->prepend($aluno->nome,'nome');
                    $data2->prepend($aluno->idmatricula,'idmatricula');                    
                    $data2->prepend($aluno->numero,'numero');
                    /* FIM DE PREENCHIMENTO...*/                    
                      /*INICIO DA AVALIACAO DE MEDIA*/    
                      $media = round($medias->sum()/$discQtd,1);
                       $obs = '';
                      if($media>=10){
                        $obs = 'Transita';
                        $count = 0;
                        foreach ($medias as $key => $value) {
                          if($value<9.5){
                            $count++;
                          }
                          if($count > 2){
                            $obs = 'Não Transita';
                            break; 
                          }
                        }

                      }else if($aluno->status !='Activo'){
                          $obs = $aluno->status;         
                      }else{
                          $obs = 'Não Transita';
                      }
                      /*FIM DA AVALIACAO ...*/

                      /*PREENCHIMENTO DOS DADOS PESSOAIS E RESULTADO DO ALUNO*/     
                       $data2->put('Genero', $aluno->sexo);
                       $data2->put('Media', $media);
                       $data2->put('OBS', $obs);
                    /*FIM DE PREENCHIMENTO*/
                  
                
                $newdata->push($data2);

                $data2 = Collect([]);
                $medias = Collect([]);

          }   
              

          $avaliacoes = collect([
          'data' => $newdata
        ]); 
          
            return  $avaliacoes;             
         
    }

    

    public static function classificaoAnual($turma_id,$trimestre){

            $turma = Turma::find($turma_id);
            $alunos = Turma::listaAlunos2($turma_id,100);          
            $discQtd = $turma->disciplinas()->count();        
            $disciplinas = Turma::listaDisciplinasCurriculares($turma->modulo_id,100);        
            $modulo = Modulo::find($turma->modulo_id);
            $curso = Curso::find($modulo->curso_id);
            $classe = Classe::find($modulo->classe_id);
            $modulo_nome = explode('ª', $modulo->nome);
            $modulo_nome = explode(' ', $modulo_nome[0]);        
            $ca_terminadas = collect([]);
            $disciplinas_terminais = $modulo->disciplinas()->where('terminal','S')->get();
            $ca_terminadas_sorted = collect([]);

            $disciplinas_frequentadas = Turma::agrupar_disciplinas_frequentadas_por_classes($curso,$classe->nome);            

            if($classe->nome == '10ª' || $classe->nome == '11ª' || $classe->nome == '12ª' || $classe->nome == '13ª'){
              $modulo_10 = Modulo::where('nome',$curso->acronimo . ' 10ª')->first();
              $disciplinas_10 = $modulo_10->disciplinas()->get();

            }
            if($classe->nome == '11ª' || $classe->nome == '12ª' || $classe->nome == '13ª'){             
              $modulo_11 = Modulo::where('nome',$curso->acronimo . ' 11ª')->first();
              $disciplinas_11 = $modulo_11->disciplinas()->get();
              $disc_terminadas_10 = $modulo_10->disciplinas()->where('terminal','S')->where('curricular','S')->get();            

            }
            if($classe->nome == '12ª' || $classe->nome == '13ª'){              
              $modulo_12 = Modulo::where('nome',$curso->acronimo . ' 12ª')->first();
              $disciplinas_12 = $modulo_12->disciplinas()->get();
              $disc_terminadas_11 = $modulo_11->disciplinas()->where('terminal','S')->where('curricular','S')->get();

            }
            if($classe->nome == '13ª'){              
              $modulo_13 = Modulo::where('nome',$curso->acronimo . ' 13ª')->first();
              $disciplinas_13 = $modulo_13->disciplinas()->get();    
              $disc_terminadas_12 = $modulo_12->disciplinas()->where('terminal','S')->where('curricular','S')->get(); 

            }
            
              
            $disciplinas_curriculares = $modulo->disciplinas()->where('curricular','S')->get();             
            $data = collect([]);   
            $data2 = collect([]);
            $discs = collect([]);
            $avaliacao = collect([]);
            $prs_info = [];
            $newdata = collect([]);
            $i = 0; 

            /*INICIO DE VERIFICACAO POR ALUNO*/            
            foreach ($alunos as $aluno){
                $aluno_ref = Aluno::find($aluno->id);                                        
                $medias = Collect([]);
                $negativas_terminais = Collect([]);
                $negativas_continuidade = Collect([]);
                $obs_recurso = collect([]);
                $obs_deficiencia = collect([]);
                $aluno_avaliacoes = Turma::avaliacoesDoAluno2($aluno->id,'S');
                
                $aluno_result = $aluno_avaliacoes['Result_10'];                                
                $aluno_avaliacoes_10 = collect([]);
                $aluno_avaliacoes_11 = collect([]);
                $aluno_avaliacoes_12 = collect([]);
                $aluno_avaliacoes_13 = collect([]);
                $aluno_turma = $aluno_ref->turmas()->get();                
                // dd($aluno_avaliacoes->where('turma_id',2));
                
                /*BUSCAR NOTAS DAS DISCIPLINAS TERMINADAS NO ANO ATERIOR*/
                if($classe->nome == '11ª' || $classe->nome == '12ª' || $classe->nome == '13ª'){
                      /*BUSCAR NOTA ANTERIOR*/                      
                      $aluno_result = $aluno_avaliacoes['Result_11'];

                      foreach ($disc_terminadas_10 as $disc_term){
                        $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                        $disc_term->id)->first();                        
                          $cfd = $aluno_avaliacao_disc['cfd_10ª'];
                          $data2->put('cfd_10_' . $disc_term->acronimo,$cfd);
                          $medias->push($cfd);

                          if(isset($aluno_avaliacao_disc['result']) && ($aluno_avaliacao_disc['result'] == 'exame1' 
                          || $aluno_avaliacao_disc['result'] == 'exame2')){                      
                          $obs_recurso->push($disc_term->acronimo);
                          }

                          if(isset($aluno_avaliacao_disc['result']) && ($aluno_avaliacao_disc['result'] == 'exame2'                          
                          || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                          || $aluno_avaliacao_disc['result'] == 'n/Continua'
                          )){                      
                          $obs_deficiencia->push($disc_term->acronimo);
                          }  
                       }                
                }
                if($classe->nome == '12ª' || $classe->nome == '13ª'){

                     $aluno_result = $aluno_avaliacoes['Result_12'];

                     foreach ($disc_terminadas_11 as $disc_term){
                        $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                        $disc_term->id)->first();                                                  
                          $cfd = $aluno_avaliacao_disc['cfd_11ª'];
                          $data2->put('cfd_11_' . $disc_term->acronimo,$cfd);
                          $medias->push($cfd);

                          if(isset($aluno_avaliacao_disc['result']) 
                          && ($aluno_avaliacao_disc['result'] == 'exame1' 
                          || $aluno_avaliacao_disc['result'] == 'exame2')){                      
                          $obs_recurso->push($disc_term->acronimo);
                          }                            
                          if(isset($aluno_avaliacao_disc['result']) 
                          && ($aluno_avaliacao_disc['result'] == 'exame2'          
                          || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                          || $aluno_avaliacao_disc['result'] == 'n/Continua'
                          )){                      
                          $obs_deficiencia->push($disc_term->acronimo);
                          } 

                       }      
                }
                
                if($classe->nome == '13ª'){ 

                      $aluno_result = $aluno_avaliacoes['Result_13'];

                      foreach ($disc_terminadas_12 as $disc_term){
                        $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                        $disc_term->id)->first();                        
                          $cfd = $aluno_avaliacao_disc['cfd_12ª'];
                          $data2->put('cfd_12_' . $disc_term->acronimo,$cfd);
                          $medias->push($cfd);

                          if((isset($aluno_avaliacao_disc['result'])) 
                          && ($aluno_avaliacao_disc['result'] == 'exame1' 
                          || $aluno_avaliacao_disc['result'] == 'exame2')){                      
                          $obs_recurso->push($disc_term->acronimo);
                          }

                          if((isset($aluno_avaliacao_disc['result'])) 
                          && ($aluno_avaliacao_disc['result'] == 'exame2'         
                          || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                          || $aluno_avaliacao_disc['result'] == 'n/Continua')
                          ){                      
                          $obs_deficiencia->push($disc_term->acronimo);
                          }                         
                       }  
                } 
                /*....FIM DA BUSCA DAS DISCIPLINAS TERMINADAS NO ANO ANTERIOR*/



                foreach ($disciplinas as $disciplina){    
                    
                    $aluno_avaliacao_turma_disc = $aluno_avaliacoes->where('disciplina_id',$disciplina->id)->first();                    
                    
                    /*BUSCAR NOTA ATERIOR DA ACTUAL DISCIPLINA */               
                    // if(!empty($data[$i])){                                                            

                      if($classe->nome=='11ª' || $classe->nome=='12ª' || $classe->nome=='13ª'){       

                              if($disciplinas_10->where('id',$disciplina->id)->isNotEmpty()){
                                $ca_10 = $aluno_avaliacao_turma_disc['ca_10ª'];
                                $data2->put('ca_10_' . $disciplina->acronimo,round($ca_10));   
                              }                     

                      }

                      if($classe->nome=='12ª' || $classe->nome=='13ª'){
                            if($disciplinas_11->where('id',$disciplina->id)->isNotEmpty()){
                              $ca_11 = $aluno_avaliacao_turma_disc['ca_11ª'];
                              $data2->put('ca_11_' . $disciplina->acronimo,round($ca_11));
                            }
                      }
                      if($classe->nome=='13ª'){                         
                            if($disciplinas_12->where('id',$disciplina->id)->isNotEmpty()){
                              $ca_12 = $aluno_avaliacao_turma_disc['ca_12ª'];
                              $data2->put('ca_12_' . $disciplina->acronimo,round($ca_12));
                            }                

                      }
                    /*FIM DA BUSCA DA NOTA ATERIOR DA ACTUAL DISCIPLINA */               
                      
                      /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/

                      
                     
                      $mn = $classe->nome;
                      $fnj ='';
                      $fj = '';
                      $cf = $aluno_avaliacao_turma_disc['mct_' . $mn];
                      $pg = $aluno_avaliacao_turma_disc['pg_' . $mn];
                      $ca = $aluno_avaliacao_turma_disc['ca_' . $mn];
                      $exame1 = $aluno_avaliacao_turma_disc['exame1_' . $mn]; 
                      $exame1 = $exame1 == '' ? '': round($exame1);                   
                      
                      /*FIM DA VERIFICAÇÃO*/
                        
                      $data2->put('fnj' .'_'. $disciplina->acronimo,$fnj);   
                      $data2->put('fj' .'_'. $disciplina->acronimo,$fj);     
                      $data2->put('cf' .'_'. $disciplina->acronimo,$cf);
                      $data2->put('pg' .'_'. $disciplina->acronimo,$pg);
                      $cfd = '';                      
                    
                      $data2->put('ca_' . $mn .'_'. $disciplina->acronimo,$ca);
                      $medias->push($ca);

                      if($classe->nome == '10ª'){
                        
                        /*10 CLASSE*/
                        if($disciplina->terminal == 'S'){
                            $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                            $disciplina->id)->first();

                            $cfd = $aluno_avaliacao_turma_disc['cfd_' . $mn];
                            $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);
                            $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);
                            $negativas_terminais->push($cfd);
                            $medias->push($cfd);
                                  // dd($aluno_avaliacao_disc);
                              
                          if(isset($aluno_avaliacao_disc['result']) 
                          && $aluno_avaliacao_disc['result'] == 'exame1'){                      
                             $obs_recurso->push($disciplina->acronimo);                                           
                          }

                          if(isset($aluno_avaliacao_disc['result']) 
                          && ($aluno_avaliacao_disc['result'] == 'exame2'                          
                          || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                          || $aluno_avaliacao_disc['result'] == 'n/Continua'
                          )){                      
                             $obs_deficiencia->push($disciplina->acronimo);
                          } 

                         }else{
                            $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                            $disciplina->id)->first();
                            
                            if(isset($aluno_avaliacao_disc['result']) 
                            && ($aluno_avaliacao_disc['result'] == 'exame2'                          
                            || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                            || $aluno_avaliacao_disc['result'] == 'n/Continua'
                            )){ 
                                $obs_deficiencia->push($disciplina->acronimo);
                              } 

                         }
                            
                      }else if($classe->nome == '11ª'){
                          /*11 CLASSE*/
                          if($disciplina->terminal == 'S'){
                              $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                              $disciplina->id)->first();

                              $cfd = $aluno_avaliacao_turma_disc['cfd_' . $mn];
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);
                              $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);
                              $negativas_terminais->push($cfd);
                              $medias->push($cfd);

                              if(isset($aluno_avaliacao_disc['result']) 
                              && $aluno_avaliacao_disc['result'] == 'exame1'){ 
                                    $obs_recurso->push($disciplina->acronimo);
                              }

                              if(isset($aluno_avaliacao_disc['result']) 
                              && ($aluno_avaliacao_disc['result'] == 'exame2'       
                              || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                              || $aluno_avaliacao_disc['result'] == 'n/Continua'
                              )){                      
                                  $obs_deficiencia->push($disciplina->acronimo);                              
                                }

                          }else{
                              $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                              $disciplina->id)->first();
                            
                              if(isset($aluno_avaliacao_disc['result']) 
                              && ($aluno_avaliacao_disc['result'] == 'exame2'                          
                              || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                              || $aluno_avaliacao_disc['result'] == 'n/Continua'
                              )){  
                                  $obs_deficiencia->push($disciplina->acronimo);
                              } 
                          }
                      
                      /*12ª CLASSE*/
                      }else if($classe->nome == '12ª'){
                         /*11 CLASSE*/
                          if($disciplina->terminal == 'S'){                            
                            $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                            $disciplina->id)->first();

                            $cfd = $aluno_avaliacao_turma_disc['cfd_' . $mn];
                            $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);
                            $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);
                            $negativas_terminais->push($cfd);
                            $medias->push($cfd);

                            if(isset($aluno_avaliacao_disc['result']) 
                            && ($aluno_avaliacao_disc['result'] == 'exame1' 
                            || $aluno_avaliacao_disc['result'] == 'exame2')){                      
                            $obs_recurso->push($disciplina->acronimo);
                            }
                             

                            if((isset($aluno_avaliacao_disc['result'])) 
                            && ($aluno_avaliacao_disc['result'] == 'exame2'             
                            || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                            || $aluno_avaliacao_disc['result'] == 'n/Continua'
                            )){ 
                            $obs_deficiencia->push($disciplina->acronimo);
                            } 

                         }else{
                            $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                            $disciplina->id)->first();
                            
                            if(isset($aluno_avaliacao_disc['result']) 
                            && ($aluno_avaliacao_disc['result'] == 'exame2'                          
                            || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                            || $aluno_avaliacao_disc['result'] == 'n/Continua'
                            )){                      
                                $obs_deficiencia->push($disciplina->acronimo);
                              } 

                         }



                      }else if($classe->nome == '13ª'){
                         /*11 CLASSE*/
                          if($disciplina->terminal == 'S'){

                            $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                            $disciplina->id)->first();

                            $cfd = $aluno_avaliacao_turma_disc['cfd_' . $mn];
                            $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);
                            $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);
                            $negativas_terminais->push($cfd);
                            $medias->push($cfd);

                            if(isset($aluno_avaliacao_disc['result']) 
                            && ($aluno_avaliacao_disc['result'] == 'exame1' 
                            || $aluno_avaliacao_disc['result'] == 'exame2')){                      
                            $obs_recurso->push($disciplina->acronimo);
                          }

                            if(isset($aluno_avaliacao_disc['result']) 
                            && ($aluno_avaliacao_disc['result'] == 'exame2'       
                            || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                            || $aluno_avaliacao_disc['result'] == 'n/Continua'
                                                        )){                      
                            $obs_deficiencia->push($disciplina->acronimo);
                            } 

                         }else{
                            $aluno_avaliacao_disc = $aluno_avaliacoes->where('disciplina_id',
                            $disciplina->id)->first();
                            
                            if(isset($aluno_avaliacao_disc['result']) 
                            && ($aluno_avaliacao_disc['result'] == 'exame2'                          
                            || $aluno_avaliacao_disc['result'] == 'n/Trans.'
                            || $aluno_avaliacao_disc['result'] == 'n/Continua'
                            )){                      
                                $obs_deficiencia->push($disciplina->acronimo);
                              } 

                         }




                      }else{

                      }/*FIM DE VERIFICACAO POR CLASSES*/        

                  // }/*FIM DE AVALIACAO DOS VALORES NULOS...*/
                  
                  $i++;

                }/*FIM DE AVALIACAO POR DISCIPLINA...*/ 

                $idade = explode('-',$aluno->idade);

                /*PREENCHIMENTO DOS DADOS PESSOAIS DO ALUNO*/
                $data2->prepend($idade[0],'idade');                    
                $data2->prepend($aluno->nome,'nome');
                $data2->prepend($aluno->idmatricula,'idmatricula');                    
                $data2->prepend($aluno->numero,'numero');
                /* FIM DE PREENCHIMENTO...*/                    
                  /*INICIO DA AVALIACAO DE MEDIA*/    
                $media = 0;
                $result = '';
                $obs = '';

                // if(sizeof($negativas_continuidade) > 2 || sizeof($negativas_terminais) > 5){
                //     $result = 'N/Trans.'; 
                                          
                // }else if($aluno->status != 'Activo'){
                //     $result = $aluno->status;     
                // }else if(sizeof($negativas_terminais) > 0 && sizeof($negativas_terminais) < 6 
                // && sizeof($negativas_continuidade) < 3){
                //     $result = 'Exame';
                //     if((($obs_deficiencia->isNotEmpty() && $obs_recurso->isEmpty()) || (sizeof($negativas_terminais) > 2 && $obs_recurso->isNotEmpty()) && $classe->nome=='12ª')){
                //       $result = 'N/Trans.';
                //     }
                    if($aluno_result == 'Exame'){                      
                      foreach ($obs_recurso as $key => $value){
                          if($key % 2 == 0){
                            $obs .= $value .',';
                          }else{
                            $obs .= $value .', '; 
                          }
                      } 
                    }else if($aluno_result == 'Trans.'){  

                    foreach ($obs_deficiencia as $key => $value){
                          if($key % 2 == 0){
                            $obs .= $value .',';
                          }else{
                            $obs .= $value .', '; 
                          }
                      } 
                    }else if($aluno_result == 'N/D'){
                      $aluno_result = $aluno->status;
                    }

                // }else{
                //     $result = 'Trans.';

                //     if((sizeof($negativas_continuidade) == 1 && $obs_recurso->isEmpty()) && $classe->nome=='12ª'){
                //       $result = 'Exame';

                //     }else if((sizeof($negativas_continuidade) == 1 && $obs_recurso->isNotEmpty()) && $classe->nome=='12ª'){
                //       $result = 'N/Trans.';
                //     }

                //     foreach ($obs_deficiencia as $key => $value) {
                //         if($key % 2 == 0){
                //             $obs .= $value .',';
                //         }else{
                //             $obs .= $value .', '; 
                //         }
                 
                //     }
                // }                        

                /*PREENCHIMENTO DOS DADOS PESSOAIS E RESULTADO DO ALUNO*/ 
                $media =  round($medias->sum()/sizeof($medias));  
                $data2->put('Genero', $aluno->sexo);
                $data2->put('Media', $media);
                $data2->put('OBS', $obs);
                $data2->put('Result', $aluno_result);
                /*FIM DE PREENCHIMENTO*/
                         
                  
                $newdata->push($data2);                
                $data2 = Collect([]);
                $medias = Collect([]);

            } 
            /*FIM DE PREENCHIMENTO DE CADA ALUNO*/  
                    
               

                $avaliacoes = collect([
                'data' => $newdata
                ]);
                      
                return  $avaliacoes;

                    
    }

   

   /*FUNCAO QUE CALCULA OS DADOS PARA PAUTAS FINAIS*/
   public static function avaliacoesDoAluno2($aluno_id,$curricular){ 
                      
         $user = auth()->user();       
         $aluno = Aluno::find($aluno_id);               
         $aluno = Aluno::find($aluno_id);               
         $aluno_turmas = $aluno->turmas()->get()->sortBy('ano_lectivo');               
         $turma = Turma::find($aluno_turmas->last()->id);                             
         $modulo = $turma->modulo()->first();
         $curso = Curso::find($modulo->curso_id);
         $modulos = $curso->Modulos()->get();
         $classe = Classe::find($modulo->classe_id);
         $epoca = Epoca::where('activo','S')->first();   
         $avaliacoesDoAluno = Turma::avaliacoesDoAluno($aluno_id);                      
         $turma_nome = explode(' ', $turma->nome);         
         $disciplinas = collect([]);        
         
         
         foreach ($modulos as $modulo){
            $disciplinas->push($modulo->disciplinas()->where('curricular',$curricular)->where('terminal','S')->get());
         }

         $disciplinas = $disciplinas->collapse();         
         $output = collect([]); 
         // dd($modulos->find(19)->disciplinas()->get());
          
        foreach ($disciplinas as $disciplina) {   
            $avaliacaoCatDoAlunoDisc = Turma::agrupar_avaliacoes_por_disciplinas($aluno,$modulos,$disciplina,$avaliacoesDoAluno);        
            $avaliacaoCatDoAlunoDisc = Turma::calcularCFD($avaliacaoCatDoAlunoDisc,$classe);
            $output->push($avaliacaoCatDoAlunoDisc);
        
        }        

        $disciplinas_frequentadas_10 = Turma::agrupar_disciplinas_frequentadas_por_classes($curso,'10ª');
        $disciplinas_frequentadas_11 = Turma::agrupar_disciplinas_frequentadas_por_classes($curso,'11ª');       
        $disciplinas_frequentadas_12 = Turma::agrupar_disciplinas_frequentadas_por_classes($curso,'12ª');
        $disciplinas_frequentadas_13 = Turma::agrupar_disciplinas_frequentadas_por_classes($curso,'13ª');       

        Turma::avaliar_a_condicao_de_transicao(10,$disciplinas_frequentadas_10,$curso,$avaliacoesDoAluno,$output); 
        Turma::avaliar_a_condicao_de_transicao(11,$disciplinas_frequentadas_11,$curso,$avaliacoesDoAluno,$output);   
        Turma::avaliar_a_condicao_de_transicao(12,$disciplinas_frequentadas_12,$curso,$avaliacoesDoAluno,$output);    
        Turma::avaliar_a_condicao_de_transicao(13,$disciplinas_frequentadas_13,$curso,$avaliacoesDoAluno,$output);
      
         // dd($output);
          return $output;

   }



      /*AGRUPAMENTO DE TODAS AVALIACOES DO ALUNO POR DISCIPLINA EM TODOS ANOS*/
        
        public static function agrupar_avaliacoes_por_disciplinas($aluno,$modulos,$disciplina,$avaliacoesDoAluno){
                /*INICIO*/

            $disciplinas = collect([]);
           
            $avaliacaoCatDoAlunoDisc = collect([]); 

            $avaliacaoCatDoAlunoDisc->put('disciplina_id',$disciplina->id);               
            $avaliacaoCatDoAlunoDisc->put('disciplina_nome',$disciplina->nome);               
            $avaliacaoCatDoAlunoDisc->put('disciplina_acronimo',$disciplina->acronimo);               
            $avaliacaoCatDoAlunoDisc->put('disciplina_categoria',$disciplina->categoria);               
            $avaliacaoCatDoAluno = $avaliacoesDoAluno->where('disciplina_id',$disciplina->id)->sortBy('modulo');         
            
            $disc_modulos = '';
                           
            foreach ($modulos as $modulo){
                $modulo_nome = explode(' ', $modulo->nome);
                $classe_nome = $modulo_nome[1];
              
                $avaliacoes = $avaliacaoCatDoAluno->where('disciplina_id',$disciplina->id)->where('modulo_id',$modulo->id);
                $disciplina_do_modulo = $modulo->disciplinas()->where('disciplina_id',$disciplina->id)->get();                   
                if($avaliacoes->isNotEmpty() && $disciplina_do_modulo->isNotEmpty()){
                    $disc_modulos .= $modulo_nome[1];
                    $avaliacao = $avaliacoes->first();                 
                    $bloqueado = $avaliacao->status == 'bloqueado' ? 'S':'N';                   
                    $avaliacaoCatDoAlunoDisc->put("bloqueado_". $modulo_nome[1],$bloqueado);
                    $avaliacaoCatDoAlunoDisc->put("avaliacao_id_". $modulo_nome[1],$avaliacao->id);
                    $avaliacaoCatDoAlunoDisc->put("modulo_". $modulo_nome[1],$avaliacao->modulo);                      
                    $avaliacaoCatDoAlunoDisc->put("aluno_id",$avaliacao->aluno_id);
                    $avaliacaoCatDoAlunoDisc->put("numero_". $modulo_nome[1],$avaliacao->numero);
                    $avaliacaoCatDoAlunoDisc->put("idmatricula",$avaliacao->idmatricula);
                    $avaliacaoCatDoAlunoDisc->put("nome",$avaliacao->nome);
                    $avaliacaoCatDoAlunoDisc->put("idade",$avaliacao->idade);

                    $avaliacaoCatDoAlunoDisc->put("turma_id_". $modulo_nome[1],$avaliacao->turma_id);
                    $avaliacaoCatDoAlunoDisc->put("fnj1_". $modulo_nome[1],$avaliacao->fnj1);
                    $avaliacaoCatDoAlunoDisc->put("mac1_". $modulo_nome[1],$avaliacao->mac1);
                    $avaliacaoCatDoAlunoDisc->put("p11_". $modulo_nome[1],$avaliacao->p11);
                    // $avaliacaoCatDoAlunoDisc->put("p12_". $modulo_nome[1],$avaliacao->p12);
                    $avaliacaoCatDoAlunoDisc->put("ct1_". $modulo_nome[1],$avaliacao->ct1);
                    $avaliacaoCatDoAlunoDisc->put("fnj2_". $modulo_nome[1],$avaliacao->fnj2);
                    $avaliacaoCatDoAlunoDisc->put("mac2_". $modulo_nome[1],$avaliacao->mac2);
                    $avaliacaoCatDoAlunoDisc->put("p21_". $modulo_nome[1],$avaliacao->p21);
                    // $avaliacaoCatDoAlunoDisc->put("p22_". $modulo_nome[1],$avaliacao->p22);                                         
                    $avaliacaoCatDoAlunoDisc->put("ct2_". $modulo_nome[1],$avaliacao->ct2);
                    $avaliacaoCatDoAlunoDisc->put("fnj3_". $modulo_nome[1],$avaliacao->fnj3);
                    $avaliacaoCatDoAlunoDisc->put("mac3_". $modulo_nome[1],$avaliacao->mac3);
                    $avaliacaoCatDoAlunoDisc->put("p31_". $modulo_nome[1],$avaliacao->p31);                      
                    $avaliacaoCatDoAlunoDisc->put("ct3_". $modulo_nome[1],$avaliacao->ct3);
                    $avaliacaoCatDoAlunoDisc->put("mct_". $modulo_nome[1],$avaliacao->sessenta);                      
                    $avaliacaoCatDoAlunoDisc->put("pg_". $modulo_nome[1],$avaliacao->quarenta);              
                    $avaliacaoCatDoAlunoDisc->put('exame1_'. $modulo_nome[1],$avaliacao->exame1);
                    $avaliacaoCatDoAlunoDisc->put('exame2_'. $modulo_nome[1],$avaliacao->exame2);
                    $avaliacaoCatDoAlunoDisc->put('exame3_'. $modulo_nome[1],$avaliacao->exame3);
                    $avaliacaoCatDoAlunoDisc->put('cfd_'. $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put('ca_'. $modulo_nome[1],round($avaliacao->notafinal)); 
                    $avaliacaoCatDoAlunoDisc->put("status",'completed');
                    $avaliacaoCatDoAlunoDisc->put("disc_modulos",$disc_modulos);

                
                }else if($avaliacoes->isEmpty() && $disciplina_do_modulo->isNotEmpty()){
                    $disc_modulos .= $modulo_nome[1];
                    $avaliacaoCatDoAlunoDisc->put("avaliacao_id_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("modulo_". $modulo_nome[1],$modulo->id);  
                    $avaliacaoCatDoAlunoDisc->put("aluno_id",$aluno->id);
                    $avaliacaoCatDoAlunoDisc->put("numero_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("idmatricula",$aluno->idmatricula);
                    $avaliacaoCatDoAlunoDisc->put("nome",$aluno->nome);
                    $avaliacaoCatDoAlunoDisc->put("idade",'');

                    $avaliacaoCatDoAlunoDisc->put("turma_id_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("fnj1_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("mac1_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("p11_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("p12_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("ct1_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("fnj2_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("mac2_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("p21_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("p22_". $modulo_nome[1],'');                                         
                    $avaliacaoCatDoAlunoDisc->put("ct2_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("fnj3_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("mac3_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("p31_". $modulo_nome[1],'');                      
                    $avaliacaoCatDoAlunoDisc->put("ct3_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("mct_". $modulo_nome[1],'');                      
                    $avaliacaoCatDoAlunoDisc->put("pg_". $modulo_nome[1],'');              
                    $avaliacaoCatDoAlunoDisc->put('exame1_'. $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put('exame2_'. $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put('exame3_'. $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put('cfd_'. $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put('ca_'. $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("status",'n/completed');
                    $avaliacaoCatDoAlunoDisc->put("disc_modulos",$disc_modulos);

                }else{

                    $avaliacaoCatDoAlunoDisc->put("avaliacao_id_". $modulo_nome[1],'');
                    $avaliacaoCatDoAlunoDisc->put("modulo_". $modulo_nome[1],$modulo->id);    
                    $avaliacaoCatDoAlunoDisc->put("aluno_id",$aluno->id);
                    $avaliacaoCatDoAlunoDisc->put("numero_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("idmatricula",$aluno->idmatricula);
                    $avaliacaoCatDoAlunoDisc->put("nome",$aluno->nome);
                    $avaliacaoCatDoAlunoDisc->put("idade",'-');

                    $avaliacaoCatDoAlunoDisc->put("turma_id_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("fnj1_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("mac1_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("p11_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("p12_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("ct1_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("fnj2_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("mac2_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("p21_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("p22_". $modulo_nome[1],'-');                                         
                    $avaliacaoCatDoAlunoDisc->put("ct2_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("fnj3_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("mac3_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("p31_". $modulo_nome[1],'-');                      
                    $avaliacaoCatDoAlunoDisc->put("ct3_". $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put("mct_". $modulo_nome[1],'-');                      
                    $avaliacaoCatDoAlunoDisc->put("pg_". $modulo_nome[1],'-');         
                    $avaliacaoCatDoAlunoDisc->put('ca_'. $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put('exame1_'. $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put('exame2_'. $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put('exame3_'. $modulo_nome[1],'-');
                    $avaliacaoCatDoAlunoDisc->put('cfd_'. $modulo_nome[1],'-');

                }
                              
                              
            }              
                              
          return $avaliacaoCatDoAlunoDisc;
                      
        }/*fim dos modulos*/  /*FIM*/ 
          

        public static function calcularCFD($avaliacaoCatDoAlunoDisc,$classe){
                  /*SE EXISTIR UMA NEGATIVA ANTES DO EXAME*/ 
                                    
                    if($avaliacaoCatDoAlunoDisc['status'] == 'completed'){  
                         $disc_modulos = $avaliacaoCatDoAlunoDisc['disc_modulos'];


                      if($disc_modulos == '10ª' || $disc_modulos == '11ª' || $disc_modulos == '12ª'){            

                         $cfd = round($avaliacaoCatDoAlunoDisc['ca_' . $disc_modulos]);                        

                         $exame1 = $avaliacaoCatDoAlunoDisc['exame1_' . $disc_modulos];
                         $exame2 = $avaliacaoCatDoAlunoDisc['exame2_' . $disc_modulos];
                        
                         $resultado = '';
                         
                         if($cfd < 10 && ($exame1 =='')){
                           $resultado = 'exame1';                          

                         }else if($cfd >= 10 && ($exame1 =='' || $exame2 =='')){
                            $resultado = 'Trans.';                             

                         }else if($cfd < 10 && ($exame2 !='' || $exame1 !='')){
                          $cfd = $exame2 != '' ? round($exame2) : round($exame1);
                          
                          if($exame2 != ''){                     
                            $resultado = $exame2 < 10 ? 'n/Trans.' : 'Trans.';
                          }else{
                            $resultado = $exame1 < 10 ? 'exame2' : 'Trans.';                            
                          }                        

                          }

                         $avaliacaoCatDoAlunoDisc->put('cfd_' . $disc_modulos,$cfd);
                         $avaliacaoCatDoAlunoDisc->put('result',$resultado);
                                       
                         $mod_arr2 = explode(',','10ª,11ª,12ª,13ª');
                         
                         foreach ($mod_arr2 as $value) {
                            if($value != $disc_modulos){
                              $avaliacaoCatDoAlunoDisc->put('cfd_' . $value,'');
                              $avaliacaoCatDoAlunoDisc->put('exame1_' . $value,'');
                              $avaliacaoCatDoAlunoDisc->put('exame2_' . $value,'');
                              $avaliacaoCatDoAlunoDisc->put('exame3_' . $value,'');
                            }
                         }

                      }else if($disc_modulos == '10ª11ª' || $disc_modulos == '11ª12ª' || $disc_modulos == '10ª11ª12ª'){
                            $mod_arr = explode('ª',$disc_modulos);
                            
                            $afd = array_reverse($mod_arr)[1] .'ª';//ANO FINAL DA DISCIPLINA
                           
                            $exame1 = $avaliacaoCatDoAlunoDisc['exame1_' . $afd];
                            $exame2 = $avaliacaoCatDoAlunoDisc['exame2_' . $afd];

                            
                            /*SE A CFD FOR NEGATIVA DEPOIS DO EXAME*/
                         if($disc_modulos == '10ª11ª'){
                            $ca1 = $avaliacaoCatDoAlunoDisc['ca_10ª'];
                            $ca2 = $avaliacaoCatDoAlunoDisc['ca_11ª'];
                            
                         
                         }else if($disc_modulos == '11ª12ª'){                            
                            $ca1 = $avaliacaoCatDoAlunoDisc['ca_11ª'];
                            $ca2 = $avaliacaoCatDoAlunoDisc['ca_12ª'];

                         }else{
                            $ca1 = $avaliacaoCatDoAlunoDisc['ca_10ª'];
                            $ca2 = $avaliacaoCatDoAlunoDisc['ca_11ª'];
                            $ca3 = $avaliacaoCatDoAlunoDisc['ca_12ª'];                            
                            
                         }

                          if(isset($ca3)){
                            // $ca1 = $ca1 == null ? $ca1 = 0 : ($ca1 == '' ? $ca1 = 0 :  $ca1);
                            // $ca2 = $ca2 == null ? $ca2 = 0 : ($ca2 == '' ? $ca2 = 0 :  $ca2);
                            // $ca3 = $ca3 == null ? $ca3 = 0 : ($ca3 == '' ? $ca3 = 0 :  $ca3);
                            $cfd = round((floatval($ca1) + floatval($ca2) + floatval($ca3))/3);
                          }else{                           
                            $cfd = round((floatval($ca1) + floatval($ca2))/2);
                          }
                          /*SE A CFD FOR NEGATIVA DEPOIS DO EXAME*/
                         if((
                             (isset($ca3) 
                          && ($ca1 >= 7 || $ca2 >= 7 || $ca3 >= 7)) || ($ca1 >= 7 || $ca2 >= 7)) 
                          && ($exame1 !='' || $exame2 !='') 
                          || ($cfd < 10 && ($exame1 !='' || $exame2 !=''))){
                            
                            $cfd = $exame2 != '' ? round($exame2) : round($exame1);
                            
                           if($exame2 != ''){                     
                             $resultado = $exame2 < 10 ? 'n/Trans.' : 'Trans.';
                           }else{
                             $resultado = $exame1 < 10 ? 'exame2' : 'Trans.';

                           }
                          /*SE UMA DAS NOTAS ANTECEDENTES FOR INFERIOR A 7 OU SE A CFD FOR NEGATIVA, ANTES DO EXAME*/
                         }else if(((
                                  (isset($ca3) 
                                  && ($ca1 < 7 || $ca2 < 7 || $ca3 < 7)) 
                                  || ($ca1 < 7 || $ca2 < 7)) && ($exame1 =='' || $exame2 =='')) 
                                  || ($cfd < 10 && ($exame1 =='' || $exame2 ==''))){


                            $resultado = $exame1 == '' ? 'exame1' : 'exame2';                        
                         
                         }else{
                            $resultado = 'Trans.';
                         }
                         $avaliacaoCatDoAlunoDisc->put('cfd_'. $afd,$cfd);
                         $avaliacaoCatDoAlunoDisc->put('result',$resultado);

                         $mod_arr2 = explode(',','10ª,11ª,12ª,13ª');                         
                           
                         foreach ($mod_arr2 as $value) {
                            if($value != $afd){
                              $avaliacaoCatDoAlunoDisc->put('cfd_' . $value,'');
                              $avaliacaoCatDoAlunoDisc->put('exame1_' . $value,'');
                              $avaliacaoCatDoAlunoDisc->put('exame2_' . $value,'');
                              $avaliacaoCatDoAlunoDisc->put('exame3_' . $value,'');
                            }
                         }

                         
                      }else{  

                          // dd($avaliacaoCatDoAlunoDisc);
                         $ca1 = floatval($avaliacaoCatDoAlunoDisc['ca_12ª']);
                         $ca2 = floatval($avaliacaoCatDoAlunoDisc['ca_13ª']);                 
                         $cfd = round(($ca1 + $ca2)/2);                        

                         if($ca1 < 10 || $ca2 < 10){
                            $resultado = 'n/Trans.';

                         }else{
                            $resultado = 'Trans.';   

                         }
                         $avaliacaoCatDoAlunoDisc->put('cfd',$cfd);
                         $avaliacaoCatDoAlunoDisc->put('result',$resultado);
                      }                     
                      
                  }else{          

                        $mods = ['10ª','11ª','12ª'];  
                             

                      foreach ($mods as $mn){
                          if($avaliacaoCatDoAlunoDisc['ca_'.$mn] != '' 
                          && $avaliacaoCatDoAlunoDisc['ca_'.$mn] != '-'){

                              $resultado = 'Trans.';                      
                              $ca = $avaliacaoCatDoAlunoDisc['ca_'.$mn];
                              $resultado = $ca < 10 ? $resultado = 'n/Continua' : $resultado = 'Continua'; 
                              $avaliacaoCatDoAlunoDisc->put('result',$resultado);
                          }else if($classe->nome == $mn && $avaliacaoCatDoAlunoDisc['ca_'.$mn] == ''){
                              $avaliacaoCatDoAlunoDisc->put('result','n/Continua');
                              break;
                          }                                                              
                      }
                } 
                        
                      
                return $avaliacaoCatDoAlunoDisc;                 
        }   
   
     public static function agrupar_disciplinas_frequentadas_por_classes($curso,$classe){
            $disciplinas = collect([]);
                if($classe == '10ª' || $classe == '11ª' || $classe == '12ª' || $classe == '13ª'){
                    $modulo_10 = Modulo::where('nome',$curso->acronimo . ' 10ª')->first();
                    $disciplinas_10 = $modulo_10->disciplinas()->where('curricular','S')->get();                 
                    $disciplinas = $disciplinas_10;
                }
                if($classe == '11ª' || $classe == '12ª' || $classe == '13ª'){             
                    $modulo_11 = Modulo::where('nome',$curso->acronimo . ' 11ª')->first();
                    $disciplinas_11 = $modulo_11->disciplinas()->where('curricular','S')->get();
                              
                    foreach ($disciplinas_10 as $disc_10) {
                      if($disc_10->pivot->terminal == 'S'){
                          $disciplinas_11->prepend($disc_10);
                      }
                    }
                          $disciplinas = $disciplinas_11;
                }
                if($classe == '12ª' || $classe == '13ª'){              
                    $modulo_12 = Modulo::where('nome',$curso->acronimo . ' 12ª')->first();
                    $disciplinas_12 = $modulo_12->disciplinas()->where('curricular','S')->get();
                    
                    foreach ($disciplinas_11 as $disc_11) {
                      if($disc_11->pivot->terminal == 'S'){
                          $disciplinas_12->prepend($disc_11);                          
                      }
                    }                  
                    $disciplinas = $disciplinas_12;
                }
                if($classe == '13ª'){              
                  $modulo_13 = Modulo::where('nome',$curso->acronimo . ' 13ª')->first();
                  $disciplinas_13 = $modulo_13->disciplinas()->where('curricular','S')->get();
                    foreach ($disciplinas_12 as $disc_12) {
                      if($disc_12->pivot->terminal == 'S'){
                          $disciplinas_13->prepend($disc_12);                         
                      }
                   } 
                  $disciplinas = $disciplinas_12;                               
              }

          return $disciplinas;
     }

     public static function avaliar_a_condicao_de_transicao($classe,$disciplinas,$curso,$avaliacoesDoAluno,$output){                     
          
        if($avaliacoesDoAluno->where('modulo', $curso->acronimo.' '.$classe.'ª')->isNotEmpty()){

                    $result = '';
                    $exames1 = 0;
                    $exames2 = 0;
                    $cont = 0;
                    $ncont = 0;
                    $tran = 0;
                    $ntran = 0;
                    $nd = 0;
                    
                    foreach ($disciplinas as $disciplina){                  
                    // if($disciplina->id == 1){
                    // }                     
                      $disc = $output->where('disciplina_id',$disciplina->id)->first();
                      if(isset($disc['result'])){

                        if($disc['result'] == 'exame1'){
                          $exames1++;
                        }
                        if($disc['result'] == 'exame2'){
                          $exames2++;
                        }
                        if($disc['result'] == 'Continua'){
                          $cont++;                 
                        }
                        if($disc['result'] == 'n/Continua'){
                          $ncont++;
                        }
                        if($disc['result'] == 'exame1'){
                          $exames1++;
                        }
                        if($disc['result'] == 'Trans.'){
                          $tran++;
                        }
                        if($disc['result'] == 'n/Trans.'){
                          $ntran++;
                        }
                      }else{
                        $nd++;
                      }              
                    }


                    /*COMECO DE AVALIACAO DE APROVACAO ANUAL*/  
                    if(($exames1 > 0 && $exames1 <=5) && ($exames2+$ncont+$ntran+$nd <= 2)){
                      $result = 'Exame';                  
                    }else if($exames2+$ncont+$ntran > 2){
                      $result = 'n/Trans.';

                    }else if(($exames2+$ncont+$ntran+$nd <= 2) && $classe < 12){

                      $result = 'Trans.';

                    }else if((($exames2 > 0 && $exames2 <=2) && ($exames2+$ncont+$ntran+$nd <= 2)) 
                    && $classe == 12){
                                          
                      $result = 'Exame';

                    }else if(($exames2+$ncont+$ntran+$nd == 0) && $classe >= 12){

                      $result = 'Trans.';

                    }else if(($exames2+$ncont+$ntran > 0) && $classe == 12){
                      $result = 'n/Trans.';                  
                    }else{
                      $result = 'N/D';                  

                    }

                    $output->put('Result_' . $classe,$result);

                  }else{
                    $output->put('Result_' . $classe,'N/D');

                  }

              return $output;
     }

      /*BUSCA TODAS AS CTS TRIMESTRAIS*/
     public static function avaliacaoTrimestralDaTurmaParaProfessor($turma,$trimestre){                 
         if($turma->ano_lectivo < 2019){
            $ct = $trimestre == 'I' ? 'ct1' :  ($trimestre == 'II' ? 'ct2' : 'notafinal');
         }else{
            $ct = $trimestre == 'I' ? 'ct1b' :  ($trimestre == 'II' ? 'ct2b' : 'notafinalb');
         }
            $mac = $trimestre == 'I' ? 'mac1' :  ($trimestre == 'II' ? 'mac2' : 'mac3');
            $p1 = $trimestre == 'I' ? 'p11' :  ($trimestre == 'II' ? 'p21' : 'p31');
            $p2 = $trimestre == 'I' ? 'p12' :  ($trimestre == 'II' ? 'p22' : 'p32');
        
          $listaModelo = DB::table('avaliacoes_view')
                        ->select('avaliacoes_view.aluno_id',
                                 'avaliacoes_view.disciplina_id',
                                 'avaliacoes_view.aluno',
                                 'avaliacoes_view.aluno_status',
                                 'avaliacoes_view.' .$mac . ' as mac',
                                 'avaliacoes_view.' .$p1 . ' as p1',
                                 'avaliacoes_view.' .$p2 . ' as p2',
                                 'avaliacoes_view.' .$ct . ' as ct'
                               )
                         ->where([['avaliacoes_view.turma_id',$turma->id]])
                         ->orderBy('avaliacoes_view.disciplina_id','ASC')
                         ->groupBy('avaliacoes_view.id')->get(); 
             
        return $listaModelo;


   }       

     /*BUSCA TODAS AS CTS TRIMESTRAIS*/
     public static function avaliacaoTrimestralDaTurma($turma_id,$trimestre){       
        
        if($trimestre == 'I'){
          $listaModelo = DB::table('avaliacoes_view')
                        ->select('avaliacoes_view.aluno_id',
                                 'avaliacoes_view.disciplina_id',
                                 'avaliacoes_view.ct1b',
                                 'avaliacoes_view.fnj1',
                                 'avaliacoes_view.fj1')
                         ->where('avaliacoes_view.turma_id',$turma_id)
                         ->orderBy('avaliacoes_view.disciplina_id','ASC')
                         ->groupBy('avaliacoes_view.id')->get();

        }else if($trimestre == 'II'){
          $listaModelo = DB::table('avaliacoes_view')
                        ->select('avaliacoes_view.aluno_id',
                                 'avaliacoes_view.disciplina_id',
                                 'avaliacoes_view.ct2b',
                                 'avaliacoes_view.fnj2',
                                 'avaliacoes_view.fj2')
                         ->where('avaliacoes_view.turma_id',$turma_id)
                         ->orderBy('avaliacoes_view.disciplina_id','ASC')
                         ->groupBy('avaliacoes_view.id')->get();
        }else{
           $listaModelo = DB::table('avaliacoes_view')
                        ->select('avaliacoes_view.aluno_id',
                                 'avaliacoes_view.disciplina_id',
                                 'avaliacoes_view.notafinalb',
                                 'avaliacoes_view.fnj3',
                                 'avaliacoes_view.fj3')
                         ->where('avaliacoes_view.turma_id',$turma_id)
                         ->orderBy('avaliacoes_view.disciplina_id','ASC')
                         ->groupBy('avaliacoes_view.id')->get();

        }
        
        return $listaModelo;

   }


   /*BUSCA TODAS AS CTS ANUAIS*/
     public static function avaliacaoTrimestraisDaTurma($turma_id){       
        
          $listaModelo = DB::table('avaliacoes_view')
                          ->select('avaliacoes_view.aluno_id',
                                   'avaliacoes_view.aluno',
                                   'avaliacoes_view.devedor',
                                   'avaliacoes_view.disciplina',
                                   'avaliacoes_view.disciplina_id',
                                   'avaliacoes_view.ct1b',
                                   'avaliacoes_view.fnj1',
                                   'avaliacoes_view.fj1',
                                   'avaliacoes_view.ct2b',
                                   'avaliacoes_view.fnj2',
                                   'avaliacoes_view.fj2',
                                   'avaliacoes_view.disciplina_id',
                                   'avaliacoes_view.notafinalb',
                                   'avaliacoes_view.fnj3',
                                   'avaliacoes_view.fj3')
                           ->where('avaliacoes_view.turma_id',$turma_id)
                           ->orderBy('avaliacoes_view.disciplina_id','ASC')
                           ->groupBy('avaliacoes_view.id')->get();      
       
          return $listaModelo;

   }




   /*BUSCA TODAS AS AVALIACOES DO ALUNO EM TODOS OS ANOS E DISCIPLINAS*/
   public static function avaliacoesDoAluno($aluno_id){
                  
                  // $turma = Turma::find($turma_id);
                  // $ano_lectivo = $turma->ano_lectivo;
                  $epoca = Epoca::where('Activo','S')->first();

                  $ano_lectivo = $epoca->ano_lectivo;
                  $listaModelo = collect([]);
                  
                  $sortedListaModelo = collect([]);

                  if($ano_lectivo < 2019){
                  
                  $listaModelo = DB::table('avaliacaos')
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->join('modulos','modulos.id','=','turmas.modulo_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')
                       ->join('aluno_turma','aluno_turma.aluno_id','=','alunos.id')           
                       ->leftjoin('users','users.id','=','avaliacaos.user_id')
                       ->where('avaliacaos.aluno_id','=',$aluno_id) 
                       ->where('turmas.ano_lectivo','<=',$epoca->ano_lectivo)

                       // ->select('avaliacaos.id')->get();
                       ->select('avaliacaos.id',
                                'avaliacaos.turma_id',
                                'modulos.nome as modulo',
                                'modulos.id as modulo_id',
                                'avaliacaos.disciplina_id',
                                'avaliacaos.aluno_id',
                                'aluno_turma.numero',
                                'turmas.ano_lectivo',
                                'alunos.idmatricula',
                                'alunos.nome',
                                DB::raw('year(now()) - year(data_de_nascimento) as idade'),
                                'disciplinas.acronimo as disciplina',
                                'avaliacaos.fnj1',
                                'avaliacaos.mac1',
                                'avaliacaos.p11',
                                'avaliacaos.p12',
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3,1) as ct1'),
                                'avaliacaos.fnj2',
                                'avaliacaos.mac2',
                                'avaliacaos.p21',
                                'avaliacaos.p22',
                                DB::raw('round((avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3,1) as cf2'),
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3,1) as ct1copy'),
                                  DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2,1) as ct2'),
                                'avaliacaos.fnj3',                                
                                'avaliacaos.mac3',
                                'avaliacaos.p31',                                
                                DB::raw('round((avaliacaos.mac3 + avaliacaos.p31)/2,1) as cf3'),
                              DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2,1) as ct2copy'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as ct3'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as mtc'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6,1) as sessenta'),
                                  'avaliacaos.p32',
                                  DB::raw('round(avaliacaos.p32 * 0.4,1) as quarenta' ),
                                  DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6 + avaliacaos.p32 * 0.4,1) as notafinal'),
                                  DB::raw('round(avaliacaos.exame1,1) as exame1' ),
                                  DB::raw('round(avaliacaos.exame2,1) as exame2' ),
                                  DB::raw('round(avaliacaos.exame3,1) as exame3' ),
                                  'avaliacaos.status'
                              )   
                       ->orderBy('disciplinas.nome','ASC')
                        ->groupBy('avaliacaos.id')->get();                        
                    }else{


                       $listaModelo = DB::table('avaliacaos')
                       ->where('avaliacaos.aluno_id','=',$aluno_id) 
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->where('turmas.ano_lectivo','<=',$epoca->ano_lectivo)
                       ->join('modulos','modulos.id','=','turmas.modulo_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')
                       ->join('aluno_turma','aluno_turma.aluno_id','=','alunos.id')           
                       ->leftjoin('users','users.id','=','avaliacaos.user_id')

                       ->select('avaliacaos.id',
                                'avaliacaos.turma_id',
                                'modulos.nome as modulo',
                                'modulos.id as modulo_id',
                                'avaliacaos.disciplina_id',
                                'avaliacaos.aluno_id',
                                'aluno_turma.numero',
                                'turmas.ano_lectivo',
                                'alunos.idmatricula',
                                'alunos.nome',
                                DB::raw('year(now()) - year(data_de_nascimento) as idade'),
                                'disciplinas.acronimo as disciplina',
                                'avaliacaos.fnj1',
                                'avaliacaos.mac1',
                                'avaliacaos.p11',                                
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11)/2,1) as ct1'),
                                'avaliacaos.fnj2',
                                'avaliacaos.mac2',
                                'avaliacaos.p21',                                
                                DB::raw('round((avaliacaos.mac2 + avaliacaos.p21)/2,1) as cf2'),
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11)/2,1) as ct1copy'),
                                  DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2,1) as ct2'),
                                'avaliacaos.fnj3',                                
                                'avaliacaos.mac3',
                                'avaliacaos.p31',                                
                                DB::raw('round((avaliacaos.mac3 + avaliacaos.p31)/2,1) as cf3'),
                                DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2,1) as ct2copy'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as ct3'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as mtc'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6,1) as sessenta'),
                                  'avaliacaos.p32',
                                DB::raw('round(avaliacaos.p32 * 0.4,1) as quarenta' ),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6 + avaliacaos.p32 * 0.4,1) as notafinal'),
                                DB::raw('round(avaliacaos.exame1,1) as exame1' ),
                                DB::raw('round(avaliacaos.exame2,1) as exame2' ),
                                DB::raw('round(avaliacaos.exame3,1) as exame3' ),
                                  'avaliacaos.status'
                              )   
                       ->orderBy('disciplinas.nome','ASC')
                        ->groupBy('avaliacaos.id')->get();                                     
                      }

                    /*MOSTRA AS AVALIACOES ANULADAS APENAS NO ANO QUE AS MESMAS OCORRERAM*/
                    foreach ($listaModelo as $lista){

                      if($lista->status == '' || $lista->status == 'bloqueado'){
                          $sortedListaModelo->push($lista);

                      }else if($lista->status == 'anulado'){

                         if($lista->ano_lectivo == $epoca->ano_lectivo){
                            $sortedListaModelo->push($lista);
                         }                          

                      }                        
                    }
                   
        return $sortedListaModelo;

   }

      
    /*BUSCA TODAS AS AVALIACOES DO ALUNO EM APENAS UMA TURMA E DISCIPLINA*/
   public static function listaAvaliacoes($turma_id,$disciplina_id,$paginate)
   {          
                         
          $turma = Turma::find($turma_id);                       
          
          if($turma->ano_lectivo < 2019){

            $listaModelo = DB::table('avaliacaos')
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')      
                       ->join('aluno_turma',[['aluno_turma.aluno_id','=','avaliacaos.aluno_id'],['aluno_turma.turma_id','=','turmas.id']])
                       ->leftjoin('users','users.id','=','avaliacaos.user_id')     
                       ->where('avaliacaos.turma_id','=',$turma_id) 
                       ->where('avaliacaos.disciplina_id','=',$disciplina_id) 
                       ->select('avaliacaos.id',                                
                                'aluno_turma.numero',
                                'alunos.nome',
                                'alunos.idade',
                                'avaliacaos.fnj1',
                                'avaliacaos.mac1',
                                'avaliacaos.p11',
                                'avaliacaos.p12',
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3,1) as ct1'),
                                'avaliacaos.fnj2',
                                'avaliacaos.mac2',
                                'avaliacaos.p21',
                                'avaliacaos.p22',
                                DB::raw('round((avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3,1) as cf2'),
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3,1) as ct1copy'),
                                  DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2,1) as ct2'),
                                'avaliacaos.fnj3',                                
                                'avaliacaos.mac3',
                                'avaliacaos.p31',                                
                                DB::raw('round((avaliacaos.mac3 + avaliacaos.p31)/2,1) as cf3'),
                                DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2,1) as ct2copy'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as ct3'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as mtc'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6,1) as sessenta'),
                                  'avaliacaos.p32',
                                DB::raw('round(avaliacaos.p32 * 0.4,1) as quarenta' ),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6 + avaliacaos.p32 * 0.4,1) as notafinal'),
                                  'aluno_turma.status',
                                  'avaliacaos.p22 as ca10',
                                  'avaliacaos.p22 as ca11',
                                  'avaliacaos.p22 as ca12'
                                 
                              )  
                       ->orderBy('aluno_turma.numero','ASC')
                        ->groupBy('avaliacaos.id')
                       ->paginate($paginate); 
                          
                    return $listaModelo;
            

          }else{

              $listaModelo = DB::table('avaliacaos')
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')      
                       ->join('aluno_turma',[['aluno_turma.aluno_id','=','avaliacaos.aluno_id'],['aluno_turma.turma_id','=','turmas.id']])                            
                       ->select('avaliacaos.id',                                
                                'aluno_turma.numero',
                                'alunos.nome',
                                'alunos.idade',
                                'avaliacaos.fnj1',
                                'avaliacaos.mac1',
                                'avaliacaos.p11',                                
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11)/2,1) as ct1'),
                                'avaliacaos.fnj2',
                                'avaliacaos.mac2',
                                'avaliacaos.p21',                                
                                DB::raw('round((avaliacaos.mac2 + avaliacaos.p21)/2,1) as cf2'),
                                DB::raw('round((avaliacaos.mac1 + avaliacaos.p11)/2,1) as ct1copy'),
                                  DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2,1) as ct2'),
                                'avaliacaos.fnj3',                                
                                'avaliacaos.mac3',
                                'avaliacaos.p31',                                
                                DB::raw('round((avaliacaos.mac3 + avaliacaos.p31)/2,1) as cf3'),
                                DB::raw('round(((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2,1) as ct2copy'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as ct3'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2,1) as mtc'),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6,1) as sessenta'),
                                  'avaliacaos.p32',
                                DB::raw('round(avaliacaos.p32 * 0.4,1) as quarenta' ),
                                DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11)/2 + (avaliacaos.mac2 + avaliacaos.p21)/2)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6 + avaliacaos.p32 * 0.4,1) as notafinal'),
                                  'aluno_turma.status',
                                  'avaliacaos.p22 as ca10',
                                  'avaliacaos.p22 as ca11',
                                  'avaliacaos.p22 as ca12'
                              )  
                       ->where('avaliacaos.turma_id','=',$turma_id) 
                       ->where('avaliacaos.disciplina_id','=',$disciplina_id) 
                       ->orderBy('aluno_turma.numero','ASC')
                        ->groupBy('avaliacaos.id')
                       ->paginate($paginate); 
                    return $listaModelo;


          }      
   }

   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           
           $listaModelo = DB::table('turmas')
                       ->join('users','users.id','=','turmas.user_id')
                       ->join('modulos','modulos.id','=','turmas.modulo_id')
                       ->join('cursos','cursos.id','=','modulos.curso_id')
                       ->join('classes','classes.id','=','modulos.classe_id')
                       ->leftjoin('Salas','salas.id','=','turmas.sala_id')                       
                       ->select('turmas.id','turmas.nome','cursos.nome as curso',
                        'classes.nome as classe','salas.nome as sala','turmas.ano_lectivo',
                        'users.name as usuario')
                       ->orderBy('turmas.ano_lectivo','desc')                       
                       ->paginate($paginate);
                       
              
                
       }else{

        $professor = Professor::where('email',$user->email)->first();
        $epoca = Epoca::where('activo','S')->first();

       $listaModelo = DB::table('disciplina_turma')
                       ->join('turmas','turmas.id','=','disciplina_turma.turma_id')
                       ->join('users','users.id','=','turmas.user_id')
                       ->join('modulos','modulos.id','=','turmas.modulo_id')
                       ->join('cursos','cursos.id','=','modulos.curso_id')
                       ->join('classes','classes.id','=','modulos.classe_id') 
                       ->join('Salas','salas.id','=','turmas.sala_id')
                       ->select('turmas.id','turmas.nome','cursos.nome as curso','classes.nome as classe','Salas.nome as sala','turmas.ano_lectivo','users.name as usuario')
                       ->orderBy('turmas.nome','desc')                      
                       ->where('disciplina_turma.professor_id','=',$professor->id)
                       ->where('turmas.ano_lectivo','=',$epoca->ano_lectivo) 
                       ->groupBy('turmas.id')   
                       ->paginate($paginate);
       }
       return $listaModelo;
   }   
   
   public static function listaDisciplinasCurriculares($modulo_id,$paginate){
       $lista = DB::table('disciplina_modulo')                                     
                   ->join('modulos','modulos.id','=','disciplina_modulo.modulo_id')                   
                   ->join('disciplinas','disciplinas.id','=','disciplina_modulo.disciplina_id')      
                   ->where('disciplina_modulo.curricular','=','S')                                    
                   ->select('disciplinas.id','disciplinas.nome as disciplina','disciplinas.acronimo','disciplina_modulo.curricular','do_curso','terminal')
                   ->where('disciplina_modulo.modulo_id','=',$modulo_id)         
                   ->groupBy('disciplinas.id')
                   ->orderBy('disciplinas.nome','ASC')
                   ->paginate($paginate);           
      return $lista;   

   }

    public static function listaDisciplinas($turma_id,$paginate)
   {
      $user = auth()->user();
      $professor = Professor::where('email',$user->email)->first();

       if($user->admin == "S"){
       
       $lista = DB::table('disciplina_turma')
                   ->join('users','users.id','=','disciplina_turma.user_id')
                   ->join('turmas','turmas.id','=','disciplina_turma.turma_id')
                   ->join('disciplinas','disciplinas.id','=','disciplina_turma.disciplina_id')      
                   ->where('disciplina_turma.turma_id','=',$turma_id) 
                   ->leftjoin('professors','professors.id','=','disciplina_turma.professor_id')
                   ->select('disciplinas.id','disciplinas.nome as disciplina','disciplinas.acronimo','professors.nome as professor','disciplina_turma.director','users.name as usuario')                   
                   ->orderBy('disciplinas.nome','ASC')
                   ->paginate($paginate);      
       }else{
            $lista = DB::table('disciplina_turma')
                   ->join('users','users.id','=','disciplina_turma.user_id')
                   ->join('turmas','turmas.id','=','disciplina_turma.turma_id')
                   ->join('disciplinas','disciplinas.id','=','disciplina_turma.disciplina_id') 
                   ->where('disciplina_turma.turma_id','=',$turma_id) 
                   ->leftjoin('professors','professors.id','=','disciplina_turma.professor_id')
                   ->select('disciplinas.id','disciplinas.nome as disciplina','disciplinas.acronimo','professors.nome as professor','users.name as usuario')
                   ->where('disciplina_turma.professor_id','=',$professor->id) 
                   ->orderBy('disciplinas.nome','ASC')
                   ->paginate($paginate);
       }
       return $lista;
   }


   public static function listaDisciplinaAlunos($turma_id,$disciplina_id,$paginate)
   {
      $user = auth()->user();
      $professor = Professor::where('email',$user->email)->first();

       if($user->admin == "S"){
       
       $lista = DB::table('aluno_disciplina')
                   ->join('turmas','turmas.id','=','aluno_disciplina.turma_id')
                   ->where('aluno_disciplina.turma_id','=',$turma_id) 
                   ->join('disciplinas','disciplinas.id','=','aluno_disciplina.disciplina_id')
                   ->where('aluno_disciplina.disciplina_id','=',$disciplina_id)    
                   ->join('alunos','alunos.id','=','aluno_disciplina.aluno_id')
                   ->leftjoin('users','users.id','=','aluno_disciplina.user_id')
                   ->select('disciplinas.id','alunos.nome as aluno','users.name as usuario')                   
                   ->orderBy('disciplinas.nome','ASC')
                   ->paginate($paginate);                 
       }else{
            $lista = DB::table('aluno_disciplina')
                   ->join('users','users.id','=','aluno_disciplina.user_id')
                   ->join('turmas','turmas.id','=','aluno_disciplina.turma_id')
                   ->join('disciplinas','disciplinas.id','=','aluno_disciplina.disciplina_id') 
                   ->where('aluno_disciplina.turma_id','=',$turma_id) 
                   ->leftjoin('alunos','alunos.id','=','aluno_disciplina.aluno_id')
                   ->select('disciplinas.id','disciplinas.nome as disciplina','disciplinas.acronimo','alunos.nome as aluno','users.name as usuario')
                   ->where('aluno_disciplina.aluno_id','=',$professor->id) 
                   ->orderBy('disciplinas.nome','ASC')
                   ->paginate($paginate);
       }
       return $lista;
   }


    public static function listaProfessorPorDisc($turma)
   {    
       $lista = DB::table('disciplina_modulo')
                       ->join('users','users.id','=','disciplina_modulo.user_id')
                       ->join('modulos','modulos.id','=','disciplina_modulo.modulo_id') 
                       ->where('disciplina_modulo.modulo_id','=',$turma->modulo_id) 
                       ->join('disciplinas','disciplinas.id','=','disciplina_modulo.disciplina_id')
                       ->join('disciplina_turma','disciplina_turma.disciplina_id','=','disciplina_modulo.disciplina_id')
                       ->join('professors','professors.id','=','disciplina_turma.professor_id')    
                       ->select('disciplinas.id','disciplinas.acronimo','disciplinas.nome as disciplina','disciplina_modulo.carga','disciplina_modulo.terminal','disciplina_modulo.do_curso',
                         'professors.nome as professor','users.name as usuario')
                       ->groupBy('disciplinas.id')
                       ->orderBy('disciplinas.nome','ASC')
                       ->get();
       
       return $lista;
   }

   public static function listaAlunos($turma_id,$paginate)
   {    
          
         $lista = DB::table('aluno_turma')
                     ->join('users','users.id','=','aluno_turma.user_id')
                     ->join('alunos','alunos.id','=','aluno_turma.aluno_id')
                     ->join('turmas','turmas.id','=','aluno_turma.turma_id') 
                     ->where('aluno_turma.turma_id','=',$turma_id) 
                     ->select('alunos.id','aluno_turma.numero','alunos.nome','alunos.devedor','aluno_turma.repetente','aluno_turma.status','aluno_turma.cargo','aluno_turma.provenienca','users.name as usuario')
                     ->orderBy('alunos.nome','ASC')
                     ->paginate($paginate);   
           return $lista;
   }

   public static function listaAlunos2($turma_id,$paginate)
   {         
           $lista = DB::table('aluno_turma')
                       ->join('users','users.id','=','aluno_turma.user_id')
                       ->join('alunos','alunos.id','=','aluno_turma.aluno_id')
                       ->join('turmas','turmas.id','=','aluno_turma.turma_id') 
                       ->where('aluno_turma.turma_id','=',$turma_id) 
                       ->select('alunos.id','alunos.idmatricula','aluno_turma.numero','alunos.nome',DB::raw('year(now())-year(alunos.data_de_nascimento) as idade'),'alunos.sexo','aluno_turma.status','users.name as usuario')
                       ->orderBy('aluno_turma.numero','ASC')
                       ->paginate($paginate);               
       
       return $lista;
   }
   
    
   
   public static function qtdAlunos($turma_id)
   {    
        $qtd = DB::table('aluno_turma')->select('aluno_turma.aluno_id')
                  ->where('aluno_turma.turma_id','=',$turma_id)->count();    
        return $qtd;
   }

   public static function alunos_turmas_actuais($modulo_id,$paginate)
   {
       $epoca = Epoca::where('Activo','S')->first();
       $user = auth()->user();
      
           $listaAlunos = DB::table('alunos')
                        ->where('alunos.modulo_id',$modulo_id)
                        ->whereNotIn('alunos.id',

                            function($query) use ($modulo_id,$epoca){
                                $query->select(DB::raw('aluno_turma.aluno_id'))->from('aluno_turma')
                                ->whereIn('aluno_turma.turma_id',

                                    function($query2) use ($modulo_id,$epoca){
                                        $query2->select(DB::raw('turmas.id'))->from('turmas')
                                        ->where('turmas.modulo_id',$modulo_id)
                                        ->where('turmas.ano_lectivo',$epoca->ano_lectivo)->get();
                                    }
                                )->get();
                          
                            }

                        )->select('alunos.id','alunos.nome','alunos.data_de_nascimento')                             
                          ->orderBy('alunos.nome','ASC')
                          ->paginate($paginate);
             $listaAlunos = $listaAlunos->sortBy(['alunos.data','alunos.nome']);           
                        
     
       return $listaAlunos;
   }


   public function buscar_turma_equivalente_ao_ano_anterior(){   
        $turma_ant = $this->buscar_nome_da_turma_equivalente_ao_ano_anterior();        
        $turma_ant = Turma::where('nome',$turma_ant)->get()->where('ano_lectivo',$this->ano_lectivo - 1)->last();

        return $turma_ant;

    }

    public function buscar_turma_equivalente_a_classe($classe){   
        $turma_ant = $this->buscar_nome_da_turma_equivalente_a_classe($classe);        
        $turma_ant = Turma::where('nome',$turma_ant)->get()->where('ano_lectivo','<',$this->ano_lectivo)->last();

        return $turma_ant;

    }
    public function buscar_nome_da_turma_equivalente_ao_ano_anterior(){     

        $mn = explode(' ', $this->nome);
        $mn_arr = explode('ª', $mn[1]);         
        $classe_ant = $mn_arr[0]-1 . 'ª';
        $mn_arr_arr = explode(' ', $this->nome);
        $turma_ant = $mn_arr_arr[0] . ' ' . $classe_ant . ' ' . $mn_arr_arr[2];        
        return $turma_ant;

    }
    public function buscar_nome_da_turma_equivalente_a_classe($classe){     

        $mn = explode(' ', $this->nome);
        $mn_arr = explode('ª', $mn[1]);         
        $classe_ant = $mn_arr[0]-1 . 'ª';
        $mn_arr_arr = explode(' ', $this->nome);
        $turma_ant = $mn_arr_arr[0] . ' ' . $classe . ' ' . $mn_arr_arr[2];        
        return $turma_ant;

    }            
   
}
