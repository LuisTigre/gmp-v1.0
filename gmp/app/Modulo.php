<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Modulo extends Model
{
   
              
   protected $fillable = ['acronimo','nome','user_id','carga',
   'do_curso','terminal','curricular','ano'];   
   

   public function user()
   {
   	return $this->belongsTo('App\user');
   }

   public function curso()
   {
    return $this->belongsTo('App\curso');
   }
   public function classe()
   {
    return $this->belongsTo('App\classe');
   }
   public function turmas()
   {
    return $this->hasMany('App\turma');
   }

   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina')->withPivot('carga','terminal','curricular');
   }

   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           
           $listaModelo = DB::table('modulos')
                       ->join('users','users.id','=','modulos.user_id')    
                       ->select('modulos.id','modulos.nome','modulos.ano','users.name as usuario')                     
                       ->orderBy('modulos.nome','ASC')
                       ->paginate($paginate);
                       // dd($listaModelo);
                
       }else{

       // $listaModelo = DB::table('professors')
       //                 ->join('users','users.id','=','professors.user_id')
       //                 ->select('professors.id','professors.titulo','professors.descricao','users.name','professors.data')
       //                 
       //                 ->where('professors.user_id','=',$user->id)
       //                 ->orderBy('professors.id','DESC')
       //                 ->paginate($paginate);
       }
       return $listaModelo;
   }

   public static function listaDisciplinas($modulo_id,$paginate)
   {
    
               $lista = DB::table('disciplina_modulo')
                       ->join('users','users.id','=','disciplina_modulo.user_id')
                       ->join('modulos','modulos.id','=','disciplina_modulo.modulo_id') 
                       ->where('disciplina_modulo.modulo_id','=',$modulo_id) 
                       ->join('disciplinas','disciplinas.id','=','disciplina_modulo.disciplina_id')    
                       ->select('disciplinas.id','disciplinas.nome as disciplina','disciplinas.acronimo','disciplina_modulo.carga','disciplina_modulo.terminal','disciplina_modulo.do_curso','disciplina_modulo.curricular','users.name as usuario')
                       ->orderBy('disciplinas.nome','ASC')
                       ->paginate($paginate);
          
       return $lista;
   }
   

   
}
