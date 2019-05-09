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


class Turma extends Model
{   
              
   protected $fillable = ['nome','periodo','user_id','modulo_id','ano_lectivo','numero','cargo','repetente','sala_id'];   
   

   public function user()
   {
   	return $this->belongsTo('App\user');
   }
   public function alunos()
   {
    return $this->belongsToMany('App\aluno')->withPivot('numero','cargo','repetente');
   }

   public function professores()
   {
    return $this->belongsToMany('App\professor','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina')->withPivot('director','disciplina_id')->withTimestamps();
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

              foreach ($disciplinas as $disciplina) {         

                  $data->push(Turma::cotacaoTrimestral($turma_id,$aluno->id,$disciplina->id,$trimestre)->toArray());                  
                                 
                  if(!empty($data[$i])){                                   
                      if($trimestre == 'I'){
                        
                        /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                        
                        $fnj1 = empty($data[$i][0]->fnj1) == true? '': $data[$i][0]->fnj1;               
                        $fj1 = empty($data[$i][0]->fj1) == true? '': $data[$i][0]->fj1;
                        $ct1 = empty($data[$i][0]->ct1) == true? '': $data[$i][0]->ct1; 
                       /*FIM DA VERIFICAÇÃO*/

                        $data2->put($disciplina->id .'_'. 'fnj1',$fnj1);   
                        $data2->put($disciplina->id .'_'. 'fj1',$fj1);     
                        $data2->put($disciplina->id .'_'. 'ct1',$ct1);

                        $medias->push($ct1);

                      }else if($trimestre == 'II'){

                        /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                        
                        $fnj2 = empty($data[$i][0]->fnj2) == true? '': $data[$i][0]->fnj2;               
                        $fj2 = empty($data[$i][0]->fj2) == true? '': $data[$i][0]->fj2;
                        $ct2 = empty($data[$i][0]->ct2) == true? '': $data[$i][0]->ct2; 
                       /*FIM DA VERIFICAÇÃO*/

                        $data2->put($disciplina->id .'_'. 'fnj2',$fnj2);   
                        $data2->put($disciplina->id .'_'. 'fj2',$fj2);     
                        $data2->put($disciplina->id .'_'. 'ct2',$ct2);   
                        
                        $medias->push($ct2);
                      }else{
                        /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                        
                        $fnj3 = empty($data[$i][0]->fnj3) == true? '': $data[$i][0]->fnj3;               
                        $fj3 = empty($data[$i][0]->fj3) == true? '': $data[$i][0]->fj3;
                        $notafinal = empty($data[$i][0]->ct3) == true? '': $data[$i][0]->ct3; 
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
                        $obs = 'Aprovado';
                        $count = 0;
                        foreach ($medias as $key => $value) {
                          if($value<9.5){
                            $count++;
                          }
                          if($count == 3){
                            $obs = 'Reprovado';
                            break; 
                          }
                        }

                      }else if($aluno->status !='Activo'){
                          $obs = $aluno->status;         
                      }else{
                          $obs = 'Reprovado';
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

    public static function buscarNotaAnterior($modulo,$retrocesso,$disc,$aluno){
            /*BUSCAR NOTAS DOS ANOS ANTERIORES*/            
            $ca = '';
            $disciplina = Disciplina::find($disc->id);
            $modulo_nome = explode('ª', $modulo->nome);
            $modulo_nome = explode(' ', $modulo_nome[0]);                  
            $classe = Classe::find($modulo->classe_id);
            $ca_anterior = collect([]);
            $ca_anterior_sorted = collect([]);
            $previous_modulo = Modulo::where('nome',$modulo_nome[0] . ' ' . 
            intVal($modulo_nome[1] - $retrocesso) . 'ª')->first();

            $aluno_info = Aluno::find($aluno->id); 

            $aluno_turma = 
            $aluno_info->turmas()->where('modulo_id',$previous_modulo->id)->get()->last();

                            
            $ca_anterior->push(Turma::cotacaoTrimestral($aluno_turma->id,$aluno->id,
            $disciplina->id,'')->toArray()); 
               
            $anterior_notafinal = empty($ca_anterior[0][0]->notafinal) == true? '': 
            $ca_anterior[0][0]->notafinal;

            $anterior_exame2 = empty($ca_anterior[0][0]->exame2) == true? '': $ca_anterior[0][0]->exame2;
            $anterior_exame1 = 
            empty($ca_anterior[0][0]->exame1) == true?  $anterior_exame2 : $ca_anterior[0][0]->exame1;                              
            if($anterior_exame1 != ''){
              $ca = $anterior_exame1;              
            }else if($anterior_exame2 != ''){
              $ca = $anterior_exame1; 
            }else{
              $ca = $anterior_notafinal; 
            }

            if($classe->nome == '11ª'){
              if($anterior_exame1 != ''){
                $ca = ['exame1' => $anterior_exame1];              
              }else if($anterior_exame2 != ''){
                $ca = ['exame2' => $anterior_exame2]; 
              }else{
                $ca = $anterior_notafinal; 
              }   
            }else if($classe->nome == '12ª'){
              if($anterior_exame1 != ''){                
                $ca = ['exame1' => $anterior_exame1];
              }else if($anterior_exame2 != ''){
                $ca = ['exame2' => $anterior_exame2]; 
              }else{
                $ca = $anterior_notafinal; 
              } 
            }else if($classe->nome == '13ª'){
              if($anterior_exame1 != ''){
                $ca = ['exame1' => $anterior_exame1];              
              }else if($anterior_exame2 != ''){
                $ca = ['exame2' => $anterior_exame2]; 
              }else{
                $ca = $anterior_notafinal; 
              } 
            }
               // if($disc->acronimo == 'Quim'){
               //    dd($ca);
               // }   
            return $ca;
    }

    public static function classificaoAnual($turma_id,$trimestre){

            $turma = Turma::find($turma_id);
            $alunos = Turma::listaAlunos2($turma_id,100);          
            $discQtd = $turma->disciplinas()->count();        
            $disciplinas = Turma::listaDisciplinasCurriculares($turma->modulo_id,100);        
            $modulo = Modulo::find($turma->modulo_id);
            $classe = Classe::find($modulo->classe_id); 
            $modulo_nome = explode('ª', $modulo->nome);
            $modulo_nome = explode(' ', $modulo_nome[0]);        
            $ca_terminadas = collect([]);
            $disciplinas_terminais = $modulo->disciplinas()->where('terminal','S')->get();
            $ca_terminadas_sorted = collect([]);
              
            if($classe->nome == '11ª' || $classe->nome == '12ª' || $classe->nome == '13ª'){ 

                /*DISCIPLINAS 10*/
                $retrocesso = 1;
                $classe->nome == '13ª' ? $retrocesso = 3 : ($classe->nome == '12ª'? $retrocesso = 2 :
                $retrocesso);


                $modulo_10 = Modulo::where('nome',$modulo_nome[0] . ' ' .
                intVal($modulo_nome[1] - $retrocesso) . 'ª')->first();         

                $disciplinas_10 = $modulo_10->disciplinas()->get();
                $disc_terminadas_10 = $modulo_10->disciplinas()->where('terminal','S')->get();

            }
            if($classe->nome == '12ª' || $classe->nome == '13ª'){        

                /*DISCIPLINAS 11*/
                $retrocesso = 1;
                $classe->nome == '13ª' ? $retrocesso = 2 : $retrocesso;

                $modulo_11 = Modulo::where('nome',$modulo_nome[0] . ' ' .
                intVal($modulo_nome[1] - $retrocesso) . 'ª')->first();

                $disciplinas_11 = $modulo_11->disciplinas()->get();
                $disc_terminadas_11 = $modulo_11->disciplinas()->where('terminal','S')->get();  

            }
            if($classe->nome == '13ª'){        

                /*DISCIPLINAS 12*/        
                $modulo_12 = Modulo::where('nome',$modulo_nome[0] . ' ' .
                intVal($modulo_nome[1] - 1) . 'ª')->first(); 
                
                $disciplinas_12 = $modulo_12->disciplinas()->get();
                $disc_terminadas_12 = $modulo_12->disciplinas()->where('terminal','S')->get();
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

                $medias = Collect([]);
                $negativas_terminais = Collect([]);
                $negativas_continuidade = Collect([]);
                $obs_recurso = collect([]);
                $obs_deficiencia = collect([]);
                
                /*BUSCAR NOTAS DAS DISCIPLINAS TERMINADAS NO ANO ATERIOR*/
                if($classe->nome == '11ª' || $classe->nome == '12ª' || $classe->nome == '13ª'){
                    foreach ($disc_terminadas_10 as $disc_term){
                      /*BUSCAR NOTA ANTERIOR*/
                      $retrocesso = 1;
                      $classe->nome == '13ª' ? $retrocesso = 3 : ($classe->nome == '12ª' ? 
                      $retrocesso = 2 : $retrocesso);
                      $ca10 = Turma::buscarNotaAnterior($modulo,$retrocesso,
                      $disc_term,$aluno);

                      /*DISCIPLINAS TERMINADAS DEPOIS DO EXAME*/
                      if(is_array($ca10)){
                        $data2->put('ca_' . intVal($modulo_nome[1] - $retrocesso) .'_'. 
                        $disc_term->acronimo,$ca10['exame1']);
                        if($ca10['exame1']<10){
                            $negativas_terminais->push(1);
                            $obs_deficiencia->push($disc_term->acronimo,$disc_term->acronimo);        
                        }
                      /*DISCIPLINAS TERMINADAS ANTES DO EXAME*/
                      }else{
                        $data2->put('ca_' . intVal($modulo_nome[1] - $retrocesso) .'_'. 
                        $disc_term->acronimo,$ca10);
                        if($ca10<10){
                          $negativas_terminais->push(1);
                          $obs_recurso->push($disc_term->acronimo,$disc_term->acronimo);
                        }
                      }
                    }                
                }
                if($classe->nome == '12ª' || $classe->nome == '13ª'){
                      $retrocesso = 1;
                      $classe->nome == '13ª' ? $retrocesso = 2 : $retrocesso;

                    foreach ($disc_terminadas_11 as $disc_term){                      
                      /*BUSCAR NOTA ANTERIOR*/                     
                      $nota_11 = Turma::buscarNotaAnterior($modulo,$retrocesso,$disc_term,
                      $aluno);
                        /*DISCIPLINAS TERMINADAS COM ANTECEDENTES OU NAO DEPOIS DO RECURSO*/
                      if(is_array($nota_11)){                                            
                        $data2->put('ca_' . intVal($modulo_nome[1] - 1) .'_'. 
                        $disc_term->acronimo,round($nota_11['exame1']));
                        if(round($nota_11['exame1']) < 10){
                          $negativas_terminais->push(1);
                          $obs_deficiencia->push($disc_term->acronimo,$disc_term->acronimo);
                        }
                       
                        /*DISCIPLINAS TERMINADAS COM ANTECEDENTE ANTES DO RECURSO*/
                      }else if($disciplinas_10->where('id',$disc_term->id)->isNotEmpty()){
                        $nota_10 = Turma::buscarNotaAnterior($modulo,$retrocesso+1,$disc_term,
                        $aluno);                                                                  
                        $cfd = round(($nota_10+$nota_11)/2);                       
                        $data2->put('ca_' . intVal($modulo_nome[1] - 2) .'_'. 
                        $disc_term->acronimo,$cfd); 
                          if($cfd<10){
                            $negativas_terminais->push(1);
                            $obs_recurso->push($disc_term->acronimo,$disc_term->acronimo);
                          }

                       /*DISCIPLINAS TERMINAIS */ 
                      }else{
                        /*DISCIPLINAS TERMINADA SEM ANTECEDENTES ANTES DO RECURSO*/
                        $data2->put('ca_' . intVal($modulo_nome[1] - 1) .'_'. 
                        $disc_term->acronimo,round($nota_11));  
                        if(round($nota_11<10)){
                            $negativas_terminais->push(1);
                            $obs_recurso->push($disc_term->acronimo,$nota_11);
                          }
                                             
                      }                       
                      
                    }  
                }
                if($classe->nome == '13ª'){                      

                    foreach ($disc_terminadas_12 as $disc_term){                      
                      /*BUSCAR NOTA ANTERIOR*/                     
                      $nota_12 = Turma::buscarNotaAnterior($modulo,1,$disc_term,
                      $aluno);
                        /*DISCIPLINAS TERMINADAS COM ANTECEDENTES OU NAO DEPOIS DO RECURSO*/
                      if(is_array($nota_12)){                                            
                        $data2->put('ca_' . intVal($modulo_nome[1] - 1) .'_'. 
                        $disc_term->acronimo,round($nota_12['exame1']));
                        if(round($nota_12['exame1']) < 10){
                          $negativas_terminais->push(1);
                          $obs_deficiencia->push($disc_term->acronimo,$disc_term->acronimo);
                        }
                       
                       /*DISCIPLINAS TERMINADAS TRIENAIS ANTES DO RECURSO*/
                      }else if($disciplinas_10->where('id',$disc_term->id)->isNotEmpty()){
                        $nota_11 = Turma::buscarNotaAnterior($modulo,2,$disc_term,
                        $aluno);
                        $nota_10 = Turma::buscarNotaAnterior($modulo,3,$disc_term,
                        $aluno);
                        $cfd = round(($nota_10+$nota_11+$nota_12)/2);                       
                        $data2->put('ca_' . intVal($modulo_nome[1] - 3) .'_'. 
                        $disc_term->acronimo,$cfd); 
                          if($cfd<10){
                            $negativas_terminais->push(1);
                            $obs_recurso->push($disc_term->acronimo,$disc_term->acronimo);
                          }

                        /*DISCIPLINAS TERMINADAS BIENAIS ANTES DO RECURSO*/
                      }else if($disciplinas_11->where('id',$disc_term->id)->isNotEmpty() && $disciplinas_10->where('id',$disc_term->id)->isEmpty()){
                        $nota_11 = Turma::buscarNotaAnterior($modulo,2,$disc_term,
                        $aluno);
                        $cfd = round(($nota_11+$nota_12)/2);                       
                        $data2->put('ca_' . intVal($modulo_nome[1] - 2) .'_'. 
                        $disc_term->acronimo,$cfd); 
                          if($cfd<10){
                            $negativas_terminais->push(1);
                            $obs_recurso->push($disc_term->acronimo,$disc_term->acronimo);
                          }

                       /*DISCIPLINAS TERMINAIS */ 
                      }else{
                        /*DISCIPLINAS TERMINADA SEM ANTECEDENTES ANTES DO RECURSO*/
                        $data2->put('ca_' . intVal($modulo_nome[1] - 1) .'_'. 
                        $disc_term->acronimo,round($nota_12));  
                        if(round($nota_12<10)){
                            $negativas_terminais->push(1);
                            $obs_recurso->push($disc_term->acronimo,$nota_12);
                          }
                                             
                      }                       
                      
                    }  
                } 
                /*....FIM DA BUSCA DAS DISCIPLINAS TERMINADAS NO ANO ANTERIOR*/



                foreach ($disciplinas as $disciplina){    
                    
                    $data->push(Turma::cotacaoTrimestral($turma_id,$aluno->id,$disciplina->id,
                    $trimestre)->toArray()); 
                    
                    /*BUSCAR NOTA ATERIOR DA ACTUAL DISCIPLINA */               
                    if(!empty($data[$i])){                                                            

                      if($classe->nome=='11ª' || $classe->nome=='12ª' || $classe->nome=='13ª'){

                          $retrocesso = 1;
                          $classe->nome == '12ª' ? $retrocesso = 2 : ($classe->nome == '13ª'? 
                          $retrocesso = 3 : $retrocesso);

                          if(!is_null(($disciplinas_10->where('id', $disciplina->id)->first()))){ 
                              $ca_10 = round(Turma::buscarNotaAnterior($modulo,$retrocesso,$disciplina,
                              $aluno));
                              $data2->put('ca_10_' . $disciplina->acronimo,$ca_10);              
                          }                          

                      }
                      if($classe->nome=='12ª' || $classe->nome=='13ª'){

                          $retrocesso = 1;
                          $classe->nome == '13ª' ? $retrocesso = 2 : $retrocesso;

                          if(!is_null(($disciplinas_11->where('id', $disciplina->id)->first()))){ 
                              $ca_11 = round(Turma::buscarNotaAnterior($modulo,$retrocesso,$disciplina,
                              $aluno));
                              $data2->put('ca_11_' . $disciplina->acronimo,$ca_11);              
                          }                          

                      }
                      if($classe->nome=='13ª'){                         

                          if(!is_null(($disciplinas_12->where('id', $disciplina->id)->first()))){ 
                              $ca_12 = round(Turma::buscarNotaAnterior($modulo,1,$disciplina,
                              $aluno));
                              $data2->put('ca_12_' . $disciplina->acronimo,$ca_12);              
                          }                          

                      }
                    /*FIM DA BUSCA DA NOTA ATERIOR DA ACTUAL DISCIPLINA */               
                      
                      /*VERIFICAR SE EXISTE VALORES NULOS NAS FALTAS E MÉDIAS... SE SIM SUBSTITUI POR VALORES VAZIOS*/
                          
                      $fnj = empty($data[$i][0]->fnj3) == true? '': $data[$i][0]->fnj3;       
                      $fj = empty($data[$i][0]->fj3) == true? '': $data[$i][0]->fj3;

                      $cf = empty($data[$i][0]->sessenta) == true? '': $data[$i][0]->sessenta; 
                      $pg = empty($data[$i][0]->quarenta) == true? '': $data[$i][0]->quarenta; 
                      $ca = empty($data[$i][0]->notafinal) == true? '': round($data[$i][0]->notafinal);   
                      $exame2 = empty($data[$i][0]->exame2) == true? '': round($data[$i][0]->exame2);   
                      $exame1 = empty($data[$i][0]->exame1) == true ? $exame2 :
                      round($data[$i][0]->exame1);
                      
                      
                      /*FIM DA VERIFICAÇÃO*/
                        
                      $data2->put('fnj' .'_'. $disciplina->acronimo,$fnj);   
                      $data2->put('fj' .'_'. $disciplina->acronimo,$fj);     
                      $data2->put('cf' .'_'. $disciplina->acronimo,$cf);
                      $data2->put('pg' .'_'. $disciplina->acronimo,$pg);
                      $cfd = '';                      
                    
                      $data2->put('ca_' . $modulo_nome[1] .'_'. $disciplina->acronimo,$ca);

                      if($classe->nome == '10ª'){
                        
                        /*10 CLASSE*/
                        if($disciplina->terminal == 'S'){
                            $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);

                              /*SE EXISTIR UMA NEGATIVA  NA 10ª ANTES DO EXAME*/
                            if(intVal($ca < 10) && $exame1 == ''){
                               $data2->put('cfd' .'_'. $disciplina->acronimo,round($ca));
                               $negativas_terminais->push($ca);
                               $obs_recurso->put($disciplina->acronimo,$disciplina->acronimo);
                               $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                              /*SE EXISTIR UMA NEGATIVA  NA 10ª DEPOIS DO EXAME*/
                            }else if(intVal($exame1 != '' && $exame1 < 10)){
                               $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                               $negativas_terminais->push($exame1);
                               $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                              /*SE EXISTIR UMA POSETIVA  NA 10ª DEPOIS DO EXAME*/
                            }else if(intVal($exame1 != '' && $exame1 >= 10)){
                               $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                              /*SE EXISTIR UMA POSETIVA  NA 10ª DEPOIS DO EXAME*/          
                            }else{
                              /*SE EXISTIR UMA POSETIVA  NA 10ª ANTES DO EXAME*/  
                               $data2->put('cfd' .'_'. $disciplina->acronimo,$ca);
                            }
                        }else{/*FIM DE DISCIPLINA TERMINAL*/
                            if(intVal($ca < 10)){
                               /*DICIPLINA DE CONTINUIDADE*/
                               $negativas_continuidade->push($ca);
                               $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);
                            } 
                        } 
                        $medias->push($ca);
                      }else if($classe->nome == '11ª'){

                          /*11 CLASSE*/
                          $disc_terminal = $disciplinas_terminais->where("id",$disciplina->id);          
                          $disc_terminada_10 = $disc_terminadas_10->where("id",$disciplina->id);          
                          $disc_anterior = $disciplinas_10->where("id",$disciplina->id);         

                          /*DISCIPLINA TERMINAL SEM PRECEDENTE*/
                          if($disciplina->terminal == 'S' && $disc_anterior->isEmpty()){
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);

                             /*SE EXISTIR UMA NEGATIVA  NA 11ª ANTES DO EXAME*/             
                             if(floatVal($ca < 10) && $exame1 == ''){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($ca));
                                  $negativas_terminais->push($ca);
                                  $obs_recurso->push($disciplina->acronimo,$disciplina->acronimo);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA NEGATIVA  NA 11ª DEPOIS DO EXAME*/ 
                             }else if(floatVal($exame1 != '' && $exame1 < 10)){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                  $negativas_terminais->push($exame1);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA NEGATIVA  NA 11ª DEPOIS DO EXAME*/ 
                             }else if(intVal($exame1 != '' && $exame1 >= 10)){           
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA POSETIVA  NA 11ª ANTES DO EXAME*/           
                             }else{
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$ca);
                             }
                          /*DISCIPLINA TERMINAL COM PRECEDENTE*/
                          }else if($disciplina->terminal == 'S' && $disc_anterior->isNotEmpty()){
                              $ca10 = $data2['ca_10_'. $disciplina->acronimo];
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);                  
                              $cfd = (floatVal($ca) + floatVal($ca10))/2;
                              
                              /*SE EXISTIR UMA NEGATIVA  NA 10ª OU 11ª  ANTES DO EXAME*/
                              if((($ca < 7 && $exame1 == '') || ($ca10 < 7 && $exame1 == '')) 
                              || (($cfd >= 7 && $cfd < 10) && $exame1 == '')){                              

                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);            
                                  $negativas_terminais->push($cfd);
                                  $obs_recurso->push($disciplina->acronimo,$disciplina->acronimo);
                                  

                              /*SE EXISTIR UMA NEGATIVA NA 11ª  DEPOIS DO EXAME*/ 
                              }else if($exame1 != '' && $exame1 < 10){                                               
                                  $cfd = $exame1;                                  
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                                  
                              /*SE EXISTIR UMA P0SETIVA NA 11ª  DEPOIS DO EXAME*/ 
                              }else if($exame1 != '' && $exame1 >= 10 ){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($exame1));
                                                                
                              /*SE EXISTIR UMA POSETIVA NA 10ª OU 11ª  ANTES DO EXAME*/ 
                              }else{
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($cfd));  
                              }

                          }else{/*INICIO DE DISCIPLINA DE CONTINUIDADE*/

                              // $ca10 = $data2['ca_10_'. $disciplina->acronimo];
                              // $cfd = (floatVal($ca) + floatVal($ca10))/2;
                              if($ca< 10){                            
                                 $negativas_continuidade->push(round($ca));
                                 $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);      
                              }
                          }/*FIM DE DISCIPLINA DE CONTINUIDADE...*/                            
                        $medias->push(round($cfd));
                                          
                      
                      /*12ª CLASSE*/
                      }else if($classe->nome == '12ª'){
                         /*11 CLASSE*/
                          $disc_terminal = $disciplinas_terminais->where("id",$disciplina->id);          
                          $disc_terminada_10 = $disc_terminadas_10->where("id",$disciplina->id);         
                          $disc_terminada_11 = $disc_terminadas_11->where("id",$disciplina->id);

                          // $qtd = intVal($disc_anterior_10->count() + $disc_anterior_10->count());          
                          $disc_anterior = $disciplinas_11->where("id",$disciplina->id);         

                          /*DISCIPLINA TERMINAL SEM PRECEDENTE*/
                          if($disciplina->terminal == 'S' && $disc_anterior->isEmpty()){
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);

                             /*SE EXISTIR UMA NEGATIVA  NA 10ª,11ª OU 12ª ANTES DO EXAME NA 12ª*/             
                             if(floatVal($ca < 10) && $exame1 == ''){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($ca));
                                  $negativas_terminais->push($ca);
                                  $obs_recurso->push($disciplina->acronimo,$disciplina->acronimo);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA NEGATIVA  NA 12ª DEPOIS DO EXAME*/ 
                             }else if(floatVal($exame1 != '' && $exame1 < 10)){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                  $negativas_terminais->push($exame1);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA POSETIVA  NA 12ª DEPOIS DO EXAME*/ 
                             }else if(intVal($exame1 != '' && $exame1 >= 10)){           
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);             

                             /*SE EXISTIR UMA POSETIVA  NA 12ª ANTES DO EXAME*/           
                             }else{
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$ca);
                             }
                          /*DISCIPLINA TERMINAL COM PRECEDENTE*/
                          }else if($disciplina->terminal == 'S' && $disc_anterior->isNotEmpty()){
                              $disc_terminada_10 = $disciplinas_10->where('id',$disciplina->id);

                              /*Cria a variavel $ca10 se a disciplina for trienal*/
                              if($disc_terminada_10->isNotEmpty()){
                                $ca10 = $data2['ca_10_'. $disciplina->acronimo];
                              }
                              /*fim....*/

                              $ca11 = $data2['ca_11_'. $disciplina->acronimo];
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);
                              
                              if(isset($ca10)){
                                $cfd = (floatVal($ca) + floatVal($ca11) + floatVal($ca10))/3;
                              }else{
                                $cfd = (floatVal($ca) + floatVal($ca11))/2;
                              }

                              /*SE EXISTIR UMA NEGATIVA  NA 10ª,11ª OU 12ª  ANTES DO EXAME*/
                              if((($ca < 7 && $exame1 == '') || ($ca11 < 7 && $exame1 == '') 
                              || (isset($ca10) && ($ca10 < 7 && $exame1 == ''))) 
                              || (($cfd >= 7 && $cfd < 10) && $exame1 == '')){                              

                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);            
                                  $negativas_terminais->push($cfd);
                                  $obs_recurso->push($disciplina->acronimo,$disciplina->acronimo);
                                  

                              /*SE EXISTIR UMA NEGATIVA NA 12ª  DEPOIS DO EXAME*/ 
                              }else if($exame1 != '' && $exame1 < 10){                                               
                                  $cfd = $exame1;                                  
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                                  
                              /*SE EXISTIR UMA P0SETIVA NA 12ª  DEPOIS DO EXAME*/ 
                              }else if($exame1 != '' && $exame1 >= 10 ){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($exame1));
                                                                
                              /*SE EXISTIR UMA POSETIVA NA 10ª,11ª OU 12ª  ANTES DO EXAME*/ 
                              }else{
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($cfd));  
                              }

                          }else{/*INICIO DE DISCIPLINA DE CONTINUIDADE*/

                              // $ca12 = $data2['ca_12_'. $disciplina->acronimo];
                              // $cfd = (floatVal($ca) + floatVal($ca12))/2;
                              if($ca < 10){                            
                                 $negativas_continuidade->push($ca);
                                 $negativas_terminais->push($ca);
                                 $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);      
                              }
                          }/*FIM DE DISCIPLINA DE CONTINUIDADE...*/                            
                        $medias->push(round($cfd));
                      }else if($classe->nome == '13ª'){
                         /*11 CLASSE*/
                          $disc_terminal = $disciplinas_terminais->where("id",$disciplina->id);          
                          $disc_terminada_10 = $disc_terminadas_10->where("id",$disciplina->id);         
                          $disc_terminada_11 = $disc_terminadas_11->where("id",$disciplina->id);

                          $disc_terminada_12 = $disc_terminadas_11->where("id",$disciplina->id);

                                  
                          $disc_anterior = $disciplinas_12->where("id",$disciplina->id);         

                          /*DISCIPLINA TERMINAL SEM PRECEDENTE*/
                          if($disciplina->terminal == 'S' && $disc_anterior->isEmpty()){
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);

                             /*SE EXISTIR UMA NEGATIVA  NA 10ª,11ª OU 12ª ANTES DO EXAME NA 12ª*/             
                             if(floatVal($ca < 10) && $exame1 == ''){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($ca));
                                  $negativas_terminais->push($ca);
                                  $obs_recurso->push($disciplina->acronimo,$disciplina->acronimo);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA NEGATIVA  NA 12ª DEPOIS DO EXAME*/ 
                             }else if(floatVal($exame1 != '' && $exame1 < 10)){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                  $negativas_terminais->push($exame1);
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);

                             /*SE EXISTIR UMA POSETIVA  NA 12ª DEPOIS DO EXAME*/ 
                             }else if(intVal($exame1 != '' && $exame1 >= 10)){           
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);             

                             /*SE EXISTIR UMA POSETIVA  NA 12ª ANTES DO EXAME*/           
                             }else{
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$ca);
                             }
                          /*DISCIPLINA TERMINAL COM PRECEDENTE*/
                          }else if($disciplina->terminal == 'S' && $disc_anterior->isNotEmpty()){
                              $disc_terminada_10 = $disciplinas_10->where('id',$disciplina->id);
                              $disc_terminada_11 = $disciplinas_11->where('id',$disciplina->id);

                              /*Cria a variavel $ca10 se a disciplina for trienal e Ca11
                               se a disciplina for bienal*/
                              if($disc_terminada_10->isNotEmpty()){                                
                                $ca10 = $data2['ca_10_'. $disciplina->acronimo];
                                $ca11 = $data2['ca_11_'. $disciplina->acronimo];

                              }else if($disc_terminada_11->isNotEmpty() && $disc_terminada_11->isEmpty()){
                                $ca11 = $data2['ca_11_'. $disciplina->acronimo];
                                
                              }
                              /*fim....*/                              

                              $ca12 = $data2['ca_12_'. $disciplina->acronimo];
                              $data2->put('exame' .'_'. $disciplina->acronimo,$exame1);                          
                              if(isset($ca11)){
                                $cfd = (floatVal($ca) + floatVal($ca11) + floatVal($ca10) + floatVal($ca12))/4;
                              }else if(isset($ca10) == false && isset($ca11)){
                                $cfd = (floatVal($ca) + floatVal($ca11) + floatVal($ca12))/3;
                              }else if(isset($ca10) == false && isset($ca11) == false){
                                $cfd = (floatVal($ca) + floatVal($ca12))/2;      
                              }else{

                              }

                              /*SE EXISTIR UMA NEGATIVA  NA 10ª,11ª,12ª ou 13ª  ANTES DO EXAME*/
                              if((($ca < 7 && $exame1 == '') || ($ca12 < 7 && $exame1 == '')    
                              || (isset($ca10) && ($ca10 < 7 && $exame1 == '')) 
                              || (isset($ca11) && ($ca11 < 7 && $exame1 == ''))) 
                              || (($cfd >= 7 && $cfd < 10) && $exame1 == '')){                       

                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$cfd);            
                                  $negativas_terminais->push($cfd);
                                  $obs_recurso->push($disciplina->acronimo,$disciplina->acronimo);   

                              /*SE EXISTIR UMA NEGATIVA NA 12ª  DEPOIS DO EXAME*/ 
                              }else if($exame1 != '' && $exame1 < 10){                                               
                                  $cfd = $exame1;                                  
                                  $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,$exame1);
                                                  
                              /*SE EXISTIR UMA P0SETIVA NA 12ª  DEPOIS DO EXAME*/ 
                              }else if($exame1 != '' && $exame1 >= 10 ){
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($exame1));
                                                                
                              /*SE EXISTIR UMA POSETIVA NA 10ª,11ª OU 12ª  ANTES DO EXAME*/ 
                              }else{
                                  $data2->put('cfd' .'_'. $disciplina->acronimo,round($cfd));  
                              }

                          }else{/*INICIO DE DISCIPLINA DE CONTINUIDADE*/

                              // $ca12 = $data2['ca_12_'. $disciplina->acronimo];
                              // $cfd = (floatVal($ca) + floatVal($ca12))/2;
                              if($ca < 10){                            
                                 $negativas_continuidade->push($ca);
                                 $negativas_terminais->push($ca);
                                 $obs_deficiencia->push($disciplina->acronimo,$disciplina->acronimo);      
                              }
                          }/*FIM DE DISCIPLINA DE CONTINUIDADE...*/                            
                        $medias->push(round($cfd));
                      }else{

                      }/*FIM DE VERIFICACAO POR CLASSES*/        

                  }/*FIM DE AVALIACAO DOS VALORES NULOS...*/
                  
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
                $media = round($medias->sum()/$discQtd);
                $result = '';
                $obs = '';

                if(sizeof($negativas_continuidade) > 2 || sizeof($negativas_terminais) > 5){
                    $result = 'N/Trans.'; 
                                          
                }else if($aluno->status != 'Activo'){
                    $result = $aluno->status;     
                }else if(sizeof($negativas_terminais) > 0 && sizeof($negativas_terminais) < 6 
                && sizeof($negativas_continuidade) < 3){
                    $result = 'Exame';
                    if((($obs_deficiencia->isNotEmpty() && $obs_recurso->isEmpty()) || (sizeof($negativas_terminais) > 2 && $obs_recurso->isNotEmpty()) && $classe->nome=='12ª')){
                      $result = 'N/Trans.';
                    }

                    foreach ($obs_recurso as $key => $value){
                        if($key % 2 == 0){
                          $obs .= $value .',';
                        }else{
                          $obs .= $value .', '; 
                        }
                    } 

                }else{
                    $result = 'Trans.';

                    if((sizeof($negativas_continuidade) == 1 && $obs_recurso->isEmpty()) && $classe->nome=='12ª'){
                      $result = 'Exame';

                    }else if((sizeof($negativas_continuidade) == 1 && $obs_recurso->isNotEmpty()) && $classe->nome=='12ª'){
                      $result = 'N/Trans.';
                    }

                    foreach ($obs_deficiencia as $key => $value) {
                        if($key % 2 == 0){
                            $obs .= $value .',';
                        }else{
                            $obs .= $value .', '; 
                        }
                 
                    }
                }                        

                /*PREENCHIMENTO DOS DADOS PESSOAIS E RESULTADO DO ALUNO*/     
                $data2->put('Genero', $aluno->sexo);
                $data2->put('Media', $media);
                $data2->put('OBS', $obs);
                $data2->put('Result', $result);
                /*FIM DE PREENCHIMENTO*/
                         
                  
                $newdata->push($data2);                
                $data2 = Collect([]);
                $medias = Collect([]);

            } 
            /*FIM DE PREENCHIMENTO DE CADA ALUNO*/  
                    
               

                $avaliacoes = collect([
                'data' => $newdata
                ]);

                // dd($avaliacoes);             
                return  $avaliacoes;          
    }

   /*BUSCA TODAS AS AVALIACOES DO ALUNO EM APENAS UM ANO E UMA DISCIPLINA*/
   public static function cotacaoTrimestral($turma_id,$aluno_id,$disciplina_id,$trimestre){    
          
           $listaModelo = DB::table('avaliacaos')
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')
                       ->join('aluno_turma','aluno_turma.aluno_id','=','alunos.id')
                       ->where('avaliacaos.turma_id','=',$turma_id) 
                       ->where('avaliacaos.aluno_id','=',$aluno_id) 
                       ->where('avaliacaos.disciplina_id','=',$disciplina_id) 
                       ->leftjoin('users','users.id','=','avaliacaos.user_id')

                       ->select('avaliacaos.id',
                                'aluno_turma.numero',
                                'alunos.idmatricula',
                                'alunos.nome',
                                DB::raw('year(data_de_nascimento)-year(now()) as idad'),
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
                                  DB::raw('round(avaliacaos.exame2,1) as exame2' )
                              )   
                       ->orderBy('disciplinas.nome','ASC')
                        ->groupBy('avaliacaos.id')->get();                 
       return $listaModelo;

   }
   /*BUSCA TODAS AS AVALIACOES DO ALUNO EM TODOS OS ANOS E DISCIPLINAS*/

   public static function avaliacoesDoAluno($aluno_id){    
          
           $listaModelo = DB::table('avaliacaos')
                       ->where('avaliacaos.aluno_id','=',$aluno_id) 
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')
                       ->join('aluno_turma','aluno_turma.aluno_id','=','alunos.id')
                       // ->where('aluno_turma.turma_id','turmas.id')                  
                       ->leftjoin('users','users.id','=','avaliacaos.user_id')

                       // ->select('avaliacaos.id')->get();
                       ->select('avaliacaos.id',
                                'avaliacaos.turma_id',
                                'avaliacaos.disciplina_id',
                                'avaliacaos.aluno_id',
                                'aluno_turma.numero',
                                'alunos.idmatricula',
                                'alunos.nome',
                                DB::raw('year(data_de_nascimento)-year(now()) as idade'),
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
                                  DB::raw('round(avaliacaos.exame2,1) as exame2' )
                              )   
                       ->orderBy('disciplinas.nome','ASC')
                        ->groupBy('avaliacaos.id')->get(); 
                        // dd($listaModelo);                
       return $listaModelo;

   }

      
    /*BUSCA TODAS AS AVALIACOES DO ALUNO EM APENAS UMA TURMA E DISCIPLINA*/
   public static function listaAvaliacoes($turma_id,$disciplina_id,$paginate)
   {    
               
           $listaModelo = DB::table('avaliacaos')
                       ->join('turmas','turmas.id','=','avaliacaos.turma_id')
                       ->join('disciplinas','disciplinas.id','=','avaliacaos.disciplina_id')
                       ->join('alunos','alunos.id','=','avaliacaos.aluno_id')      
                       ->join('aluno_turma','aluno_turma.aluno_id','=','avaliacaos.aluno_id')                      
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
                                  DB::raw('round((((avaliacaos.mac1 + avaliacaos.p11 + avaliacaos.p12)/3 + (avaliacaos.mac2 + avaliacaos.p21 + avaliacaos.p22)/3)/2 + (avaliacaos.mac3 + avaliacaos.p31)/2)/2 * 0.6 + avaliacaos.p32 * 0.4,1) as notafinal')
                              )  
                       ->orderBy('aluno_turma.numero','ASC')
                        ->groupBy('avaliacaos.id')
                       ->paginate($paginate);      
                    return $listaModelo;
   }

   public static function listaModelo($paginate,$ano_lectivo)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           
           $listaModelo = DB::table('turmas')
                       ->join('users','users.id','=','turmas.user_id')
                       ->join('modulos','modulos.id','=','turmas.modulo_id')
                       ->join('cursos','cursos.id','=','modulos.curso_id')
                       ->join('classes','classes.id','=','modulos.classe_id')
                       ->join('Salas','salas.id','=','turmas.sala_id')
                       ->where('turmas.ano_lectivo','=',$ano_lectivo)    
                       ->select('turmas.id','turmas.nome','cursos.nome as curso','classes.nome as classe','salas.nome as sala','turmas.ano_lectivo','users.name as usuario')
                       ->orderBy('turmas.nome','desc')                       
                       ->paginate($paginate);
                       
              
                
       }else{

        $professor = Professor::where('email',$user->email)->first();


       $listaModelo = DB::table('disciplina_turma')
                       ->join('turmas','turmas.id','=','disciplina_turma.turma_id')
                       ->join('users','users.id','=','turmas.user_id')
                       ->join('modulos','modulos.id','=','turmas.modulo_id')
                       ->join('cursos','cursos.id','=','modulos.curso_id')
                       ->join('classes','classes.id','=','modulos.classe_id') 
                       ->select('turmas.id','turmas.nome','cursos.nome as curso','classes.nome as classe','turmas.ano_lectivo','users.name as usuario')
                       ->orderBy('turmas.nome','desc')                      
                       ->where('disciplina_turma.professor_id','=',$professor->id)
                       ->where('turmas.ano_lectivo','=',$ano_lectivo)    
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
                   ->select('disciplinas.id','disciplinas.nome as disciplina','disciplinas.acronimo','professors.nome as professor','users.name as usuario')                   
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
                     ->select('alunos.id','aluno_turma.numero','alunos.nome','aluno_turma.status','aluno_turma.cargo','users.name as usuario')
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

   
}
