<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Turma;

class Instituicao extends Model
{  
              
   protected $fillable = ['nome','sigla','logo','lema','numero','email','telefone1','telefone2','telefone3','director_instituicao','director_instituicao_sexo','director_pedagogico','director_pedagogico_sexo','user_id'];
         
  
   public function user()
   {
    return $this->belongsTo('App\User');
   } 
  
   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaAlunos = DB::table('instituicaos')                                   
                        ->leftjoin('users','users.id','=','instituicaos.user_id')            
                        ->select('instituicaos.id','instituicaos.nome',
                        'instituicaos.sigla',
                        'instituicaos.lema','instituicaos.telefone1','instituicaos.email','instituicaos.director_instituicao','instituicaos.director_pedagogico','users.name as usuario')            
                        ->orderBy('instituicaos.nome','ASC')
                        ->paginate($paginate);
                      
       }
       return $listaAlunos;
   }


  

  
   
}
