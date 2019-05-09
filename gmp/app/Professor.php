<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Professor extends Model
{
   use SoftDeletes;
              
   protected $fillable = ['nome','telefone','email'];
   
   protected $dates = ['deleted_at'];

   public function user()
   {
    return $this->belongsTo('App\user');
   }
   public function curso()
   {
   	return $this->belongsTo('App\curso');
   }

   public function turmas()
   {
    return $this->belongsToMany('App\turma','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }

   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }



   public static function listaProfessores($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaProfessores = DB::table('professors')                       
                       ->select('professors.id','professors.nome',
                        'professors.telefone','professors.email')
                       ->whereNull('deleted_at')
                       ->orderBy('professors.id','DESC')
                       ->paginate($paginate);
       }else{

       $listaProfessores = DB::table('professors')
                       ->select('professors.id','professors.nome',
                        'professors.telefone','professors.email')
                       ->whereNull('deleted_at')
                       ->where('professors.email','=',$user->email)
                       ->orderBy('professors.id','DESC')
                       ->paginate($paginate);
       }       
       return $listaProfessores;
   }
   
}
