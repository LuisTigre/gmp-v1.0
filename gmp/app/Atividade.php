<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Atividade extends Model
{
   
   protected $fillable = ['nome','epoca','planned_start_time','planned_end_time','descricao'];   

   public function user()
   {
    return $this->belongsTo('App\User');
   }
   public function epoca()
   {
    return $this->belongsTo('App\Epoca');
   }
   public function grupo()
   {
   	return $this->belongsTo('App\AtividadeGrupo');
   }
   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('atividades')
                       ->join('users','users.id','=','atividades.user_id')
                       ->select('atividades.id','atividades.nome','atividades.descricao','atividades.prazo_inicial','atividades.prazo_final','users.name as usuario')               
                       ->orderBy('atividades.id','DESC')
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
