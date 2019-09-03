<?php

namespace App\Imports;

use App\Aluno;
use App\Modulo;
use App\Turma;
use App\Http\Controllers\Admin\TurmaAlunoController;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlunosImport implements ToModel,WithHeadingRow
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
    	set_time_limit(240);


        if((isset($row['nome']) && isset($row['turma'])) && (isset($row['idmatricula']) && isset($row['numero']))){          
    	    
            $this->cadastrarAluno($row);        
        }else{
            return null;
        }
        
    	
    }

   public function cadastrarAluno($row){

        $user = auth()->user();        

        if($user->admin == 'S'){        
            $turma = turma::where('nome',$row['turma'])->first();
            if(!is_null($turma)){
                $modulo = $turma->modulo()->first();
            }else{
                 $turma_nome =explode(' ', $row['turma']);
                 $modulo_nome = $turma_nome[0] . ' ' . $turma_nome[2];
                 $modulo = Modulo::where('nome',$modulo_nome)->first();
            }           
            $ano = date('Y')-intVal($row['idade']);
            $data = date($ano . '-01-01');                      
       
            $aluno = Aluno::where('idmatricula',$row['idmatricula'])->get();

            if($aluno->isNotEmpty() && !is_null($turma)){
		            
                $turmas = $aluno->first()->turmas()->get()->where('id',$turma->id);
                if($turmas->isNotEmpty()){
                    $turma = $turmas->first();                    
                    $data = [
                        'aluno_id' =>  ['aluno_id'=>$aluno->first()->id],
                        'turma_id' => $turma->id,
                        'numero' => $row['numero'],
                        'status' => $row['estado'] == '' ? 'Activo' : 'Desistido',
                        'provenienca' => ''                    
                    ];     
                    TurmaAlunoController::inscrever_aluno_na_turma($data);
                    $turma->alunos()->updateExistingPivot($aluno->first()->id,['numero' => $row['numero']]);
                }else{
                    $turma->alunos()->updateExistingPivot($aluno->first()->id,['numero' => $row['numero']]);
                }
            }else if($aluno->isEmpty() && !is_null($turma)){
                $aluno_novo = new Aluno([
                'nome' => $row['nome'],
                'data_de_nascimento' => $data,
                'idmatricula' => $row['idmatricula'],                
                'telefone' => 900000000,
                'encarregado_tel' => 900000000,               
                'user_id' => $user->id,         
                'modulo_id' => $modulo->id,
                'sexo' => $row['sexo']
                ]);

                $aluno_novo->save();

                $data = [
                        'aluno_id' => ['aluno_id'=>$aluno_novo->id],
                        'turma_id' => $turma->id,
                        'numero' => $row['numero'],                 
                        'status' => $row['estado'] == '' ? 'Activo' : 'Desistido',               
                        'provenienca' => '',                 
                    ];

                TurmaAlunoController::inscrever_aluno_na_turma($data);
                $turma->alunos()->updateExistingPivot($aluno_novo->id,['numero' => $row['numero']]);         
            }else{
                return null;
            }

       }

   }

   public function headingRow(): int
    {
        return 3;
    }
}
