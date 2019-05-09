<?php

namespace App\Imports;

use App\Professor;
use App\Modulo;
use App\User;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProfessorsImport implements ToModel,WithHeadingRow
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
        || !isset($row['telefone'])
        || !isset($row['sexo'])
        ){
    		return null;
    	}else{
            $last_teacher = Professor::all()->last();
            $ano = Date('Y'); 
            $email = 'professor' . intVal($last_teacher->id + 1) .'@mail.com';

            $userAccount = new User([
            'name' => $row['nome'],         
            'email' => $email,
            'password' => '123456',
            'professor' => 'S'      
        ]); 
            $userAccount->save();
	    	return new Professor([
	        'nome' => $row['nome'],	        
	        'telefone' => $row['telefone'],
	        'email' => $email,
            'user_id' => $userAccount->id,
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
