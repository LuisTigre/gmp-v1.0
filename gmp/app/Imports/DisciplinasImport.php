<?php

namespace App\Imports;

use App\Disciplina;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DisciplinasImport implements ToModel,WithHeadingRow
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
    	
        
    	if(!isset($row['nome'])
        || !isset($row['acronimo'])
        || !isset($row['categoria'])
        && $row['categoria'] != 'Técnica, Tecnológica e Prática'
        && $row['categoria'] != 'Sociocultural'
        && $row['categoria'] != 'Científica'       
        )
        {
    	   return null;
    	}else{
          $disciplina = Disciplina::where('acronimo',$row['acronimo'])->get();          

          if($disciplina->isEmpty()){            
	    	return new Disciplina([
	        'nome' => $row['nome'],	        
	        'acronimo' => $row['acronimo'],
	        'user_id' => $user->id,	        
	        'categoria' => $row['categoria']
          ]);     
           }else{
             return null;            
           }
     }
    }
   }

   public function headingRow(): int
    {
        return 3;
    }
}
