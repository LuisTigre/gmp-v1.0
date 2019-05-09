<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Curso extends Model
{
   
              
   protected $fillable = ['nome','acronimo','user_id','coordenador','nome_instituto_mae','director_instituto_mae','area_id'];   

   public function user()
   {
   	return $this->belongsTo('App\user');
   }
   public function modulos()
   {
    return $this->hasMany('App\modulo');
   }
   public function professor()
   {
    return $this->belongsTo('App\professor');
   }
   public function area()
   {
    return $this->belongsTo('App\area');
   }

   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('cursos')
                       ->join('users','users.id','=','cursos.user_id')   
                       ->join('professors','professors.id','=','cursos.professor_id')   
                       ->join('areas','areas.id','=','cursos.area_id')   
                       ->select('cursos.id','cursos.nome','cursos.acronimo','areas.nome as area','professors.nome as coordenador','cursos.nome_instituto_mae','cursos.director_instituto_mae','users.name as usuario')                       
                       ->orderBy('cursos.nome','ASC')
                       ->paginate($paginate);
       }else{

       // $listaModelo = DB::table('professors')
       //                 ->join('users','users.id','=','professors.user_id')
       //                 ->select('professors.id','professors.titulo','professors.descricao','users.name','professors.data')
       //                 ->whereNull('deleted_at')
       //                 ->where('professors.user_id','=',$user->id)
       //                 ->orderBy('professors.id','DESC')
       //                 ->paginate($paginate);
       }
       return $listaModelo;
   }
   /*public static function $listaModeloSite($paginate,$busca = null)
   {
      if($busca){
         $listaModelo = DB::table('professores')
                       ->join('users','users.id','=','professores.user_id')
                       ->select('professores.id','professores.titulo','professores.descricao','users.name as autor','professores.data')
                       ->whereNull('deleted_at')
                       ->whereDate('data','<=',date('Y-m-d'))
                       ->where(function($query) use ($busca){
                        $query->orWhere('titulo','like','%'.$busca.'%')
                              ->orWhere('descricao','like','%'.$busca.'%');
                       })
                       ->orderBy('data','DESC')
                       ->paginate($paginate);

      
      }else{
         $listaModelo = DB::table('professores')
                       ->join('users','users.id','=','professores.user_id')
                       ->select('professores.id','professores.titulo','professores.descricao','users.name as autor','professores.data')
                       ->whereNull('deleted_at')
                       ->whereDate('data','<=',date('Y-m-d'))
                       ->orderBy('data','DESC')
                       ->paginate($paginate);

       
     }
     return $listaModelo;
   }*/
}
