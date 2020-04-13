<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Epoca extends Model
{
   
   protected $fillable = ['trimestre','ano_lectivo','activo','epoca','planned_start_time','planned_end_time','descricao','fechado'];   

   public function user()
   {
    return $this->belongsTo('App\User');
   }
   public function atividades()
   {
   	return $this->belongsTo('App\Atividade');
   }
   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('epocas')
                       ->join('users','users.id','=','epocas.user_id')
                       ->select('epocas.id','epocas.trimestre','ano_lectivo','epocas.activo',
                        'epocas.fechado','users.name as usuario')     
                       ->orderBy('epocas.id','DESC')
                       ->paginate($paginate);
       }else{

       $listaModelo = DB::table('epocas')
                       ->join('users','users.id','=','epocas.user_id')
                       ->select('epocas.id','epocas.nome','epocas.descricao','users.name as usuario','epocas.data')                       
                       ->where('epocas.user_id','=',$user->id)
                       ->orderBy('epocas.id','DESC')
                       ->paginate($paginate);
       }
       return $listaModelo;
   }

   public static function activo(){
        return Epoca::where('activo','S')->first();

   }   
   
}
