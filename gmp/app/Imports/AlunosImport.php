<?php

namespace App\Imports;

use App\Aluno;
use App\Modulo;
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
    	$user = auth()->user(); 
        if($user->admin == 'S'){
       
    	$modulo = Modulo::where('nome',$row['modulo'])->first();
        $ano = date('Y')-intVal($row['idade']);
        $data = date($ano . '-01-01');
                      
        if(isset($row['idmatricula'])){
          $aluno = Aluno::where('idmatricula',$row['idmatricula'])->get();   
        }
    	if(!isset($row['nome']) || $aluno->isNotEmpty()){
    		return null;
    	}else{
	    	return new Aluno([
	        'nome' => $row['nome'],
	        'data_de_nascimento' => $data,
	        'idmatricula' => $row['idmatricula'],
	        'repetente' => $row['repetente'],
	        'telefone' => $row['telefone'],
	        'encarregado_tel' => $row['encarregado_tel'],
	        'email' => $row['email'],
	        'user_id' => $user->id,	        
	        'modulo_id' => $modulo->id,
	        'sexo' => $row['sexo']
        ]);     
     }
    }
   }

   public function headingRow(): int
    {
        return 3;
    }
}
