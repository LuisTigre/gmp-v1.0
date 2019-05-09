<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class AtividadeGrupo extends Model
{
   
   protected $fillable = ['nome'];   

   public function user()
   {
    return $this->belongsTo('App\user');
   }
   public function atividades()
   {
   	return $this->hasMany('App\atividade');
   }
   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('atividade_grupos')
                       ->join('users','users.id','=','atividade_grupos.user_id')
                       ->select('atividade_grupos.id','atividade_grupos.nome','users.name as usuario')
                       ->orderBy('atividade_grupos.nome','DESC')
                       ->paginate($paginate);   
      
       }else{

       $listaModelo = DB::table('atividades')
                       ->join('users','users.id','=','atividades.user_id')
                       ->select('atividades.id','atividades.titulo','atividades.descricao','users.name','atividades.data')
                       ->whereNull('deleted_at')
                       ->where('atividades.user_id','=',$user->id)
                       ->orderBy('atividades.id','DESC')
                       ->paginate($paginate);
       }
       return $listaModelo;
   }
   
   
}
