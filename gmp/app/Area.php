<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Area extends Model
{
   
              
   protected $fillable = ['nome','acronimo','user_id'];   

   public function user()
   {
   	return $this->belongsTo('App\User');
   }   
   public function cursos()
   {
    return $this->hasMany('App\Curso');
   }
   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('areas')
                       ->leftjoin('users','users.id','=','areas.user_id')                   
                       ->select('areas.id','areas.nome','areas.acronimo','users.name as usuario')                       
                       ->orderBy('areas.nome','ASC')
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
