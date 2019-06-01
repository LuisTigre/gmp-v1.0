<?php

namespace App\Imports;

use App\Aluno;
use App\Avaliacao;
use App\Modulo;
use App\Turma;
use App\Professor;
use App\Disciplina;
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
        
        $user = auth()->user();

            
    	if(!isset($row['nome_completo']) & !isset($row['disciplina']) & !isset($row['turma']) & !isset($row['ano_lectivo']) & !isset($row['ano_lectivo']) ){

            return null;
                 
        }else{
            $ano_lectivo = intVal($row['ano_lectivo']);            
            $turma = Turma::where('nome',$row['turma'])->get()->where('ano_lectivo',$ano_lectivo)->first();

            if(is_null($turma)){
                dd("TURMA INEXISTENTE !!!
                    PARA VOLTAR AO MENU PRINCIPAL CLIQUE EM SETA 'Voltar' <--- ");
            }       
            $alunos = $turma->alunos()->get();  

            $disciplina = Disciplina::where('acronimo',$row['disciplina'])->first();
            $director_turma = $turma->professores()->where('director','s')->first();         
            $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first();
            $aluno = $alunos->where('nome',$row['nome_completo'])->first();            
            
            if($professor != null && $aluno != null){

            if($user->admin == 'S' 
            // || $user->email == $director_turma->email 
            || $user->email == $professor->email){ 
                $avaliacao = Avaliacao::where('turma_id',2)->where('disciplina_id',9)->where('aluno_id',1)->get();                
                $avaliacao = Avaliacao::where('turma_id',$turma->id)->where('disciplina_id',$disciplina->id)->where('aluno_id',$aluno->id)->get();
                                             
            if($avaliacao->isNotEmpty()){
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
             
             return new Avaliacao([         
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

   public function headingRow(): int
    {
        return 13;
    }
}
