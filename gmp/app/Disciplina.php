<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Disciplina extends Model
{  
              
   protected $fillable = ['acronimo','nome','user_id','categoria']; 

   public function user()
   {
    return $this->belongsTo('App\user');
   }   
   public function modulos()
   {
   	return $this->belongsToMany('App\modulo')->withPivot('carga', 'duracao');
   }
   public function turmas()
   {
    return $this->belongsToMany('App\turma')->withPivot('director','disciplina_id')->withTimestamps();
   }
   public function professores()
   {
    return $this->belongsToMany('App\professor','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }


   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('disciplinas')
                       ->join('users','users.id','=',
                        'disciplinas.user_id')   
                       ->select('disciplinas.id','disciplinas.acronimo','disciplinas.nome','disciplinas.acronimo','disciplinas.categoria','users.name as usuario')                     
                       ->orderBy('disciplinas.nome','ASC')
                       ->paginate($paginate);
       }else{

       // $listaModelo = DB::table('professors')
       //                 ->join('users','users.id','=','professors.user_id')
       //                 ->select('professors.id','professors.titulo','professors.descricao','users.name','professors.data')
       //                 ->where('professors.user_id','=',$user->id)
       //                 ->orderBy('professors.id','DESC')
       //                 ->paginate($paginate);
       }
       return $listaModelo;
   }


   public function buscarAlunosDaDisciplina($turma)
   {
    
       $user = auth()->user();
       $alunos = Turma::listaAlunosRepOuNao($turma->id,100);
       $alunos_rep = $alunos->where('repetente','S');

       $alunos_sorted = $alunos->where('repetente','N');       
       $alunos_sorted_arr = [];       
       $data = [];    

       foreach ($alunos_sorted as $alunos_sorted) {
                $data = ["id"=>$alunos_sorted->id,
              "idmatricula"=>$alunos_sorted->idmatricula,
              "numero"=>$alunos_sorted->numero,
              "nome"=>$alunos_sorted->nome,
              "idade"=>$alunos_sorted->idade,
              "sexo"=>$alunos_sorted->sexo,
              "status"=>$alunos_sorted->status,
              "repetente"=>$alunos_sorted->repetente,
              "usuario"=>$alunos_sorted->usuario];

              array_push($alunos_sorted_arr, $data);          
       }
       foreach ($alunos_rep as $repetente){       
       
           $avaliacaoDaDisciplina = Turma::avaliacoesDoAluno2($repetente->id,'S')->where('disciplina_id',$this->id)->first();           
           if(isset($avaliacaoDaDisciplina['result']) 
            && ($avaliacaoDaDisciplina['result'] == 'Tran.' 
            || $avaliacaoDaDisciplina['result'] == 'Continua')){            
           }else{

             $data = ["id"=>$repetente->id,
              "idmatricula"=>$repetente->idmatricula,
              "numero"=>$repetente->numero,
              "nome"=>$repetente->nome,
              "idade"=>$repetente->idade,
              "sexo"=>$repetente->sexo,
              "status"=>$repetente->status,
              "repetente"=>$repetente->repetente,
              "usuario"=>$repetente->usuario];               
             array_push($alunos_sorted_arr, $data);
           }
       }
      
       $listaModelo = collect(['data'=>$alunos_sorted_arr]); 
       // dd($listaModelo); 
       $listaDisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();
       return $listaModelo;
   }
   
}
