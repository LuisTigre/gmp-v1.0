<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Disciplina extends Model
{  
              
   protected $fillable = ['turma_id','disciplina_id','professor_id','user_id','director']; 

   public function user()
   {
    return $this->belongsTo('App\User');
   }   
   
    public function disciplina()
   {
    return $this->belongsTo('App\Disciplina','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function turma()
   {
    return $this->belongsTo('App\Turma','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function professor()
   {
    return $this->belongsToMany('App\Professor','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }

   public function avaliacoes(){
     dd($this);
   }


   public static function listaModelo($paginate)
   {
    
      
   }


   
}
   

