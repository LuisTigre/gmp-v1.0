<?php

namespace App\Imports;

use App\Aluno;
use App\Avaliacao;
use App\Modulo;
use App\Turma;
use App\Professor;
use App\Disciplina;
use App\Classe;
use App\Epoca;
use App\Sala;
use App\Events\AvaliacaoChanged;
use App\Http\Controllers\Admin\TurmaAlunoController;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AvaliacaosImport implements WithHeadingRow, ToModel
{
    /**
    * WithMappedCells,
    * @param Collection $collection
    */
    // public function mapping(): array
    // {
    //     return [
    //         'modulo'  => 'C1'
    //     ];
    // }
    

    public function model(array $row){
        // dd($row);
        set_time_limit(3600);
        $user = auth()->user();       
        if(isset($row['pauta'])){
            $this->importar_avaliacoes_via_pauta_anual($row,$user);          
        }else if(isset($row['migrado'])){       
            $this->importar_avaliacoes_da_disciplina_via_pauta_anual($row,$user);          
        }else if(isset($row['transferido'])){
            $this->importar_avaliacoes_via_ficha_de_aproveitamento($row,$user);
        }else{  
            $avaliacao = $this->importar_avaliacoes_via_pauta_trimestral($row,$user);
            if(!is_null($avaliacao)){
                $professor = Professor::find($avaliacao->professor_id);                          
                event(new AvaliacaoChanged($professor));              

            }
        }           
   }
    

    public function importar_avaliacoes_da_disciplina_via_pauta_anual($row,$user){

            if(isset($row['turma']) && isset($row['nome_completo']) && isset($row['disciplina'])){
                            
                $epoca = Epoca::where('Activo','S')->first();
                $turma = Turma::where('nome',$row['turma'])->get()->where('ano_lectivo',$epoca->ano_lectivo)->last();  
                

                $aluno = Aluno::where('nome',$row['nome_completo'])->get()->first();
                  
                  if(!is_null($aluno) && !is_null($turma)){
                    $disciplina = Disciplina::where('acronimo',$row['disciplina'])->first();

                    $modulo = $turma->modulo;
                    $classe = $modulo->classe;
                    $curso = $modulo->curso;                              
                    
                    $turma_10 = $this->cadastrar_turma_do_ano_anterior($turma,$aluno,$user,'10ª',$row['10_lectivo']);                                    

                    if((!is_null($turma_10) && $turma_10->ano_lectivo == $turma->ano_lectivo) && !is_null($disciplina)){
                    $new_classe = $turma_10->modulo->classe;
                                          
                        if($classe->nome == '13ª' && $new_classe->nome == '12ª'){                
                                                                        
                            if(round(floatval($row['12a'])) >= 10){                              
                               $avaliacao_12 = $this->cadastrar_as_notas('12a',$row,$turma_10,$disciplina,$user,$aluno);
                               $avaliacao_12->status = 'bloqueado';
                               $avaliacao_12->save();              
                            }else{

                            }
                        }
                        if($classe->nome == '12ª' && ($new_classe->nome == '11ª' || $new_classe->nome == '12ª')){
                            if($new_classe->nome == '12ª'){
                                $turma_10 = $this->cadastrar_turma_do_ano_anterior($turma_10,$aluno,$user,'11ª',$row['10_lectivo']);       

                            }                         
                            if(round(floatval($row['11a'])) >= 10){                              
                               $avaliacao_11 = $this->cadastrar_as_notas('11a',$row,$turma_10,$disciplina,$user,$aluno);
                               $avaliacao_11->status = 'bloqueado';
                               $avaliacao_11->save();              
                            }else{

                            }
                        }
                        if(round($row['10a']) >= 10 && ($new_classe->nome == '10ª' || $new_classe->nome == '11ª' || $new_classe->nome == '12ª')){
                            if($new_classe->nome == '11ª' || $new_classe->nome == '12ª'){
                                $turma_10 = $this->cadastrar_turma_do_ano_anterior($turma_10,$aluno,$user,'10ª',$row['10_lectivo']);       

                            }
                            $avaliacao_10 = $this->cadastrar_as_notas('10a',$row,$turma_10,$disciplina,$user,$aluno);
                            $avaliacao_10->status = 'bloqueado';
                            $avaliacao_10->save();
                        }

                    }else if(!is_null($disciplina)){ 
                    
                        $this->cadastrar_as_notas('10a',$row,$turma_10,$disciplina,$user,$aluno);

                        if($classe->nome == '12ª'){                       
                            $turma_11 = $this->cadastrar_turma_do_ano_anterior($turma,$aluno,$user,'11ª',$row['11_lectivo']); 
                            $this->cadastrar_as_notas('11a',$row,$turma_11,$disciplina,$user,$aluno);              
                        }
                        if($classe->nome == '13ª'){                       
                            $turma_12 = $this->cadastrar_turma_do_ano_anterior($turma,$aluno,$user,'12ª',$row['12_lectivo']);
                            $this->cadastrar_as_notas('12a',$row,$turma_12,$disciplina,$user,$aluno);              
                        }


                    }


                  }
            }
    }

    public function importar_avaliacoes_via_ficha_de_aproveitamento($row,$user){
        if(isset($row['turma']) && isset($row['nome_completo'])){
                $turma = Turma::where('nome',$row['turma'])->get()->where('ano_lectivo',$row['ano_lectivo'])->last();
                    
                    if(!is_null($turma)){
                        // dd($row);
                        $modulo = $turma->modulo;
                        $aluno = Aluno::where('nome',$row['nome_completo'])->get()->where('idmatricula',$row['idmatricula'])->first();
                        $turmas = $aluno->turmas()->where('id',$turma->id)->get();
                       if($turmas->isEmpty()){
                            dd("ALUNO INEXISTENTE EM TURMA REFERIDA !!!
                                CERTIFIQUE-SE DE QUE O ALUNO FAZ PARTE DA REFERIDA TURMA
                                PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
                       }
                        $disciplinas = $modulo->disciplinas()->get();
                        $newAvaliacao;

                        foreach($disciplinas as $key => $disc){

                                $newAvaliacao = Avaliacao::where('disciplina_id',$disc->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id)->last();                        
                                if(!is_null($newAvaliacao) && $newAvaliacao->status !='bloqueado'){
                                    if(isset($row[strtolower($disc->acronimo)])){      
                                        $ca = round(floatval($row[strtolower($disc->acronimo)]),1);
                                        $p2 = $ca;
                                        if($turma->ano_lectivo >= 2019){
                                            $p2 = null;
                                        }
                                        if($row['avaliacao'] == 'CT1'){
                                            $newAvaliacao->update([     
                                            'user_id' => $user->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,
                                            'mac1' => $ca,
                                            'p11' => $ca,
                                            'p12' => $p2
                                            ]);   
                                             
                                        }else if($row['avaliacao'] == 'CT2'){
                                            $newAvaliacao->update([     
                                            'user_id' => $user->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,                                    
                                            'mac2' => $ca,
                                            'p21' => $ca,                       
                                            'p22' => $p2                      
                                            ]);   
                                        
                                        }else if($row['avaliacao'] == 'CT3'){
                                            $newAvaliacao->update([     
                                            'user_id' => $user->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,                          
                                            'mac3' => $ca,
                                            'p31' => $ca                                          
                                            ]);   
                                        }else if($row['avaliacao'] == 'PG'){
                                            $newAvaliacao->update([     
                                            'user_id' => $user->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,                                
                                            'p32' => $ca                                          
                                            ]);   
                                        }
                                    
                                    }    
                                }else{
                                    $disc->acronimo = 'tlp';
                                    if(isset($row[strtolower($disc->acronimo)])){                                
                                        $ca = round(floatval($row[strtolower($disc->acronimo)]),1);
                                        $p2 = round($ca,1);
                                        if($turma->ano_lectivo >= 2019){
                                            $p2 = null;
                                        }
                                        if($row['avaliacao'] == 'CT1'){
                                            // dd($row);                                    
                                            $newAvaliacao = new Avaliacao([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,
                                            'mac1' => $ca,
                                            'p11' => $ca,
                                            'p12' => $p2
                                            ]);   
                                             
                                        }else if($row['avaliacao'] == 'CT2'){
                                            $newAvaliacao = new Avaliacao([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,                          
                                            'p12' => $p2,
                                            'mac2' => $ca,
                                            'p21' => $ca,                       
                                            'p22' => $p2,                       
                                            ]);   
                                        
                                        }else if($row['avaliacao'] == 'CT3'){
                                            $newAvaliacao = new Avaliacao([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,                          
                                            'mac3' => $ca,
                                            'p31' => $ca,                                          
                                            ]);   
                                        }else if($row['avaliacao'] == 'PG'){
                                            $newAvaliacao = new Avaliacao([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,                                
                                            'p32' => $ca,                                          
                                            ]);   
                                        }                                 
                                        $newAvaliacao->save();      
                                    }
                                }
                                    
                        }
                        return $newAvaliacao;
                         
                    }else{
                        return null;
                    }

            }else{
                return null;
            }     
    }


    public function importar_avaliacoes_via_pauta_anual($row,$user){
        if(isset($row['turma']) && isset($row['nome_completo'])){
                $ano_anterior = intVal($row['ano_lectivo']-1);
                $turma = Turma::where('nome',$row['turma'])->get()->where('ano_lectivo',$row['ano_lectivo'])->last();               
                    if(!is_null($turma)){
                  
                        // dd($row);
                        $modulo = $turma->modulo;
                        $classe = Classe::find($modulo->classe_id);
                        $aluno = Aluno::where('nome',$row['nome_completo'])->get()->where('idmatricula',$row['idmatricula'])->first();
                       if($aluno->turmas()->where('id',$turma->id)->get()->isEmpty()){
                            dd("ALUNO INEXISTENTE EM TURMA REFERIDA !!!
                                CERTIFIQUE-SE DE QUE O ALUNO FAZ PARTE DA REFERIDA TURMA
                                PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
                       }
                        $disciplinas = $modulo->disciplinas()->get();
                        $newAvaliacao;

                        foreach($disciplinas as $key => $disc){
                                $newAvaliacao = Avaliacao::where('disciplina_id',$disc->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id)->last();
                                
                                if(!is_null($newAvaliacao) && $newAvaliacao->status !='bloqueado'){
                                    if(isset($row[strtolower($disc->acronimo)])){      
                                        $ca = floatval($row[strtolower($disc->acronimo)]);
                                        $p2 = $ca;
                                        if($turma->ano_lectivo >= 2019){
                                            $p2 = null;
                                        }
                                        $newAvaliacao->update([     
                                            'user_id' => $user->id,                                            
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,
                                            'mac1' => $ca,
                                            'p11' => $ca,
                                            'p12' => $p2,
                                            'mac2' => $ca,
                                            'p21' => $ca,                       
                                            'p22' => $p2,                       
                                            'mac3' => $ca,
                                            'p31' => $ca,
                                            'p32' => $ca                        
                                        ]);   
                                        // $newAvaliacao->save();      
                                    }
                                }else{
                                    if(isset($row[strtolower($disc->acronimo)])){      
                                        $ca = floatval($row[strtolower($disc->acronimo)]);
                                        $p2 = $ca;
                                        if($turma->ano_lectivo >= 2019){
                                            $p2 = null;
                                        }
                                        $newAvaliacao = new Avaliacao([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,
                                            'mac1' => $ca,
                                            'p11' => $ca,
                                            'p12' => $p2,
                                            'mac2' => $ca,
                                            'p21' => $ca,                       
                                            'p22' => $p2,                       
                                            'mac3' => $ca,
                                            'p31' => $ca,
                                            'p32' => $ca                        
                                        ]);   
                                        $newAvaliacao->save();      
                                    }
                                }
                                
                        }
                        return $newAvaliacao;
                         
                    }else{
                        return null;
                    }

            }else{
                return null;
            }     
    }
    
    public function importar_avaliacoes_via_pauta_trimestral($row,$user){
        if(!isset($row['nome_completo']) & !isset($row['disciplina']) & !isset($row['turma']) & !isset($row['ano_lectivo']) & !isset($row['ano_lectivo']) ){

                return null;
                     
            }else{
                $ano_lectivo = intVal($row['ano_lectivo']); 
                $row['turma'] = trim($row['turma']);           
                $row['disciplina'] = trim($row['disciplina']);           
                $row['ano_lectivo'] = trim($row['ano_lectivo']);           
                $row['turma'] = trim($row['turma']);           
                $row['nome_completo'] = trim($row['nome_completo']);           
                // $row['turma']->rtrim();  

                $turma = Turma::where('nome',$row['turma'])->get()->where('ano_lectivo',$ano_lectivo)->first();
               // dd($row);

                if(is_null($turma)){
                    dd("TURMA '".$row['turma']."' INEXISTENTE !!!
                        PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
                }       
                $alunos = $turma->alunos()->get();  

                $disciplina = Disciplina::where('acronimo',$row['disciplina'])->first();
                if(is_null($disciplina)){
                    dd("DISCIPLINA INEXISTENTE !!!
                        PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
                }       
                $director_turma = $turma->professores()->where('director','s')->first();         
                $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first();

                $aluno = $alunos->where('nome',$row['nome_completo'])->first();
                if(!is_null($aluno)){                    
                    if($aluno->turmas()->where('id',$turma->id)->get()->isEmpty()){
                        dd("ALUNO INEXISTENTE EM TURMA REFERIDA !!!
                            CERTIFIQUE-SE DE QUE O ALUNO FAZ PARTE DA REFERIDA TURMA
                            PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
                    }
                }            
                if($professor != null && $aluno != null){

                if($user->admin == 'S' 
                // || $user->email == $director_turma->email 
                || $user->email == $professor->email){                                   
                    
                    $avaliacao = Avaliacao::where('turma_id',$turma->id)->where('disciplina_id',$disciplina->id)->where('aluno_id',$aluno->id)->get();    
                                                 
                if(($avaliacao->isNotEmpty() && $avaliacao->first()->status !='bloqueado') || ($avaliacao->isNotEmpty() && $user->admin == 'S')){
                
                $avaliacao = $avaliacao->first();
                $data = [
                'professor_id' => $professor->id,
                'user_id' => $user->id,
                'turma_id' => $turma->id,
                'disciplina_id' => $disciplina->id,
                'aluno_id' => $aluno->id,
                'mac1' => $row['mac1'],
                'p11' => $row['p11'],
                'p12' => $row['p12'],
                'fnj1' => $row['faltas1'],            
                'mac2' => $row['mac2'],
                'p21' => $row['p21'],
                'p22' => $row['p22'],
                'fnj2' => $row['faltas2'],           
                'mac3' => $row['mac3'],
                'p31' => $row['p31'],
                'p32' => $row['pg'],
                'fnj3' => $row['faltas3']];
                $avaliacao->update($data);                
                return $avaliacao;              

                }else{
                 
                 $avaliacao =  new Avaliacao([         
                'professor_id' => $professor->id,
                'user_id' => $user->id,
                'turma_id' => $turma->id,
                'disciplina_id' => $disciplina->id,
                'aluno_id' => $aluno->id,
                'mac1' => $row['mac1'],
                'p11' => $row['p11'],
                'p12' => $row['p12'],
                'fnj1' => $row['faltas1'],            
                'mac2' => $row['mac2'],
                'p21' => $row['p21'],
                'p22' => $row['p22'],
                'fnj2' => $row['faltas2'],           
                'mac3' => $row['mac3'],
                'p31' => $row['p31'],
                'p32' => $row['pg'],
                'fnj3' => $row['faltas3']                        
                ]);                 
                 $avaliacao->save();                 
                 return $avaliacao;

                }            
              }
                }else{
                    if(is_null($alunos)){
                    dd("PROFESSOR OU ALUNO INEXISTENTE !!!
                        PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
                    } 
                }
            }
    }

     
    public function cadastrar_turma_do_ano_anterior($turma,$aluno,$user,$classe,$ano_lectivo){
                    
                    if(!is_null($turma)){ 
                        $turmas = $aluno->turmas()->get();
                        $modulo = $turma->modulo;
                        $curso = $modulo->curso;
                        $classe_actual = $modulo->classe;
                        $nao_faz_parte = $turmas->where('id',$turma->id)->isEmpty();
                        $turma_nome = $turma->buscar_nome_da_turma_equivalente_a_classe($classe);

                       if($nao_faz_parte){                                                  
                            $turma_actual =  $aluno->buscarTurmaActualDaClasse($turma);
                            if(!is_null($turma_actual)){                                

                                $turma_actual->alunos()->updateExistingPivot($aluno->id,['repetente'=>'S']);
                                $turma_actual->save();
                                return $turma_actual;
                            }else{
                                $turmas = $modulo->turmas()->get()->where('ano_lectivo',$turma->ano_lectivo);
                                foreach ($turmas as $turma) {
                                   $faz_parte = $turma->alunos()->get()->where('id',$aluno->id)->isNotEmpty();                   
                                   if($faz_parte){
                                        $turma_anterior = $aluno->buscarTurmaDaClasse($turma,$classe,$ano_lectivo);                                      
                                        break;
                                   }
                                }
                                if(!is_null($faz_parte) && $faz_parte == false){
                                    dd("ALUNO $aluno->nome INEXISTENTE EM TURMA REFERIDA !!!
                                        CERTIFIQUE-SE DE QUE O ALUNO FAZ PARTE DA REFERIDA TURMA
                                        PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");

                                }

                            }
                       }else{                        
                            $turma_anterior = $aluno->buscarTurmaDaClasse($turma,$classe,$ano_lectivo);
                            
                       }

                        
                        if(!isset($turma_anterior) || is_null($turma_anterior)){                      
                                                                         
                          
                            $turma_anterior = $turma->buscar_turma_equivalente_a_classe($classe,$ano_lectivo);                            
                            if(is_null($turma_anterior)){
                                                               
                                $sala = Sala::all()->first();
                                $modulo_anterior = $modulo->moduloDaClasse($classe); 
                                $newTurma = new Turma([
                                "modulo_id" => $modulo_anterior->id,
                                "user_id" => $user->id,
                                "nome" => $turma_nome,
                                "ano_lectivo" => $ano_lectivo,                               
                                "sala_id" => $sala->id                                
                                ]);                         
                                $newTurma->save();                               
                                $newTurma->disciplinas()->saveMany($modulo_anterior->disciplinas()->get());

                                $data = [
                                 "turma_id" => $newTurma->id,
                                  "aluno_id" => [
                                    0 => $aluno->id
                                  ],
                                  "numero" => null,
                                  "cargo" => null,
                                  "user_id" => $user->id,
                                  "provenienca" => null
                                ];

                                TurmaAlunoController::inscrever_aluno_na_turma($data);
                                return $newTurma;
                            }else{
                           
                                $data = [
                                 "turma_id" => $turma_anterior->id,
                                  "aluno_id" => [
                                    0 => $aluno->id
                                  ],
                                  "numero" => null,
                                  "cargo" => null,
                                  "user_id" => $user->id,
                                  "provenienca" => null
                                ];

                                TurmaAlunoController::inscrever_aluno_na_turma($data);
                            }
                               return $turma_anterior; 

                        }else{
                            return $turma_anterior; 
                        }                
                        
                        
                    }else{
                        return null;
                    }


    }

    public function inserir_turma_na_db($turma_nome,$modulo_id,$ano_lectivo,$sala_id){
            $newTurma = new Turma([
            "modulo_id" => $modulo_id,
            "user_id" => $user->id,
            "nome" => $turma_nome,
            "ano_lectivo" => $ano_lectivo,                               
            "sala_id" => $sala_id                                
            ]);
            $newTurma->save(); 
            return $newTurma;               

    }
    public function cadastrar_as_notas($opcao,$row,$turma,$disc,$user,$aluno){
        $ca = round(floatval($row[strtolower($opcao)]),1);

                    $newAvaliacao = Avaliacao::where('disciplina_id',$disc->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id)->last();


                    $p2 = $ca;
                    if($turma->ano_lectivo >= 2019){
                        $p2 = null;
                    }

                     $newAvaliacao = Avaliacao::where('disciplina_id',$disc->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id)->last();
                                
                                if(!is_null($newAvaliacao)){                                        
                                    if(isset($row[strtolower($opcao)])){      
                                        $ca = round(floatval($row[strtolower($opcao)]),1);                                        
                                        $p2 = $ca;
                                        if($turma->ano_lectivo >= 2019){
                                            $p2 = null;
                                        }
                                        
                                        $newAvaliacao->update([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,
                                            'mac1' => $ca,
                                            'p11' => $ca,
                                            'p12' => $p2,
                                            'mac2' => $ca,
                                            'p21' => $ca,                       
                                            'p22' => $p2,                       
                                            'mac3' => $ca,
                                            'p31' => $ca,
                                            'p32' => $ca                             
                                        ]);   
                                        $newAvaliacao->save();      
                                    }
                                }else{
                                    if(isset($row[strtolower($opcao)])){      
                                        $ca = round(floatval($row[strtolower($opcao)]),1    );                                       
                                        $p2 = $ca;
                                        if($turma->ano_lectivo >= 2019){
                                            $p2 = null;
                                        }
                                        $newAvaliacao = new Avaliacao([     
                                            'user_id' => $user->id,
                                            'turma_id' => $turma->id,
                                            'disciplina_id' => $disc->id,
                                            'aluno_id' => $aluno->id,
                                            'mac1' => $ca,
                                            'p11' => $ca,
                                            'p12' => $p2,
                                            'mac2' => $ca,
                                            'p21' => $ca,                       
                                            'p22' => $p2,                       
                                            'mac3' => $ca,
                                            'p31' => $ca,
                                            'p32' => $ca                              
                                        ]);   
                                        $newAvaliacao->save();      
                                    }
                                }
                                
                        
                        return $newAvaliacao; 

    }

    public function headingRow(): int
    {
        return 13;
        // return 5;
    }
}
