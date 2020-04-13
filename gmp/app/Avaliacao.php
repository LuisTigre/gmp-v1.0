<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Avaliacao extends Model
{
   
              
   protected $fillable = ['professor_id','user_id','disciplina_id','professor_id',
                          'turma_id','aluno_id','mac1','p11','p12','fnj1','fj1',
                          'mac2','p21','p22','fnj2','fj2','mac3','p31','p32',
                          'fnj3','fj3','exame1','exame2','exame3','status'];   

   public function user()
   {
    return $this->belongsTo('App\User');
   }
   public function turma()
   {
   	return $this->belongsTo('App\Turma');
   }
    public function professor()
   {
    return $this->belongsTo('App\Professor');
   }
    public function disciplina()
   {
    return $this->belongsTo('App\Disciplina');
   }
   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('avaliacaos')
                       ->join('users','users.id','=','avaliacaos.user_id')   
                       ->select('avaliacaos.id','avaliacaos.nome','avaliacaos.acronimo','users.name as usuario')
                       ->orderBy('avaliacaos.nome','ASC')
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
   public static function neg_dez($value){
            $cor = 'black';
         if(($value != '-' && $value != '') && $value < 10){                    
            
            $cor = 'red';                    
            
         }else{
            $cor = 'black';                   

         }
         return $cor;
       }
}
