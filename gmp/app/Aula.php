<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Aula extends Model
{   
              
   protected $fillable = ['nome','descricao','professor_id','user_id','disciplina_id','turma_id','sala_id','tempo_id'];   
   
   public function user()
   {
    return $this->belongsTo('App\user');
   }
   public function turma()
   {
   	return $this->belongsTo('App\turma');
   }
   public function professor()
   {
    return $this->belongsTo('App\professor');
   }
   public function sala()
   {
    return $this->belongsTo('App\sala');
   }
   public function disiciplina()
   {
    return $this->belongsTo('App\disiciplina');
   }
   public function tempo()
   {
    return $this->belongsTo('App\tempo');
   }
   public function modulo()
   {
    return $this->belongsTo('App\modulo');
   }
   public static function listaModelo($turma,$paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){          
           $listaModelo = DB::table('aulas')
                       ->join('users','users.id','=','aulas.user_id')
                       ->join('salas','salas.id','=','aulas.sala_id')
                       ->join('disciplinas','disciplinas.id','=','aulas.disciplina_id')   
                       ->join('professors','professors.id','=','aulas.professor_id')
                       // ->join('tempos','tempos.id','=','aulas.tempo_id')
                       ->select('aulas.id','disciplinas.nome as disciplina','aulas.nome','professors.nome as professor','salas.nome as sala','users.name as usuario')        
                       ->where('aulas.turma_id',$turma)
                       ->orderBy('aulas.nome','ASC')
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

   public static function aulas_nao_alocadas($turma)
   {    
                
           $listaModelo = DB::table('aulas')
                       ->join('users','users.id','=','aulas.user_id')
                       ->join('salas','salas.id','=','aulas.sala_id')
                       ->join('disciplinas','disciplinas.id','=','aulas.disciplina_id')   
                       ->join('professors','professors.id','=','aulas.professor_id')
                        // ->join('tempos','tempos.id','=','aulas.tempo_id')
                       ->select('aulas.id','disciplinas.nome as disciplina','professors.nome as professor','salas.nome as sala','users.name as usuario')        
                       ->where('aulas.turma_id',$turma)
                       ->orderBy('aulas.nome','ASC')->get();
                       // ->paginate($paginate);       
       
       return $listaModelo;
    
 }

   public static function aulas_alocadas($turma)
   {    
          $listaModelo = DB::table('aulas')
                       ->join('users','users.id','=','aulas.user_id')
                       ->join('salas','salas.id','=','aulas.sala_id')
                       ->join('disciplinas','disciplinas.id','=','aulas.disciplina_id')   
                       ->join('professors','professors.id','=','aulas.professor_id')
                       ->join('tempos','tempos.id','=','aulas.tempo_id')
                       ->select('aulas.id','disciplinas.nome as disciplina','professors.nome as professor','salas.nome as sala','users.name as usuario','tempos.dia','tempos.hora')        
                       ->where('aulas.turma_id',$turma)
                       ->orderBy('aulas.nome','ASC')->get();
                       // ->paginate($paginate);       
       
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
