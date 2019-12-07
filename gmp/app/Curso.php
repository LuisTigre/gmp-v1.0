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
   	return $this->belongsTo('App\User');
   }
   public function modulos()
   {
    return $this->hasMany('App\Modulo');
   }
   public function professor()
   {
    return $this->belongsTo('App\Professor');
   }
   public function area()
   {
    return $this->belongsTo('App\Area');
   }

   public static function listaModelo($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaModelo = DB::table('cursos')
                       ->join('professors','professors.id','=','cursos.professor_id')   
                       ->join('areas','areas.id','=','cursos.area_id')   
                       ->leftjoin('users','users.id','=','cursos.user_id')   
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

     public function disciplinas($paginate)
   {          
            
            $curso_disciplina = DB::table('curso_disciplina')                          
                       ->where('curso_disciplina.curso_id',$this->id)                     
                       ->select('curso_disciplina.disciplina_id','curso_disciplina.disciplina'
                        ,'curso_disciplina.categoria','curso_disciplina.carga','curso_disciplina.curricular',
                        'curso_disciplina.curso_acr','curso_disciplina.classe')              
                       // ->groupBy('curso_disciplina.disciplina_id')
                       ->orderBy('curso_disciplina.modulo_id','ASC')->get();
         
           $listaModelo = DB::table('curso_disciplina')                          
                       ->where('curso_disciplina.curso_id',$this->id)                     
                       ->select('curso_disciplina.disciplina_id','curso_disciplina.disciplina',
                        'curso_disciplina.disc_acr','curso_disciplina.carga as carga_10','curso_disciplina.carga as carga_11',
                        'curso_disciplina.carga as carga_12','curso_disciplina.carga as carga_13'
                        ,'curso_disciplina.curricular','curso_disciplina.categoria')              
                       ->groupBy('curso_disciplina.disciplina_id')
                       ->orderBy('curso_disciplina.categoria','ASC')->paginate($paginate);
                       // ->paginate($paginate);
        
          // $lista = $listaModelo;


          foreach ($listaModelo as $disciplina) {
                  $carga_10 = $curso_disciplina->where('curso_acr',$this->acronimo)->where('classe','10ª')->where('disciplina_id',$disciplina->disciplina_id)->first();
                  $carga_11 = $curso_disciplina->where('curso_acr',$this->acronimo)->where('classe','11ª')->where('disciplina_id',$disciplina->disciplina_id)->first();
                  $carga_12 = $curso_disciplina->where('curso_acr',$this->acronimo)->where('classe','12ª')->where('disciplina_id',$disciplina->disciplina_id)->first();
                  $carga_13 = $curso_disciplina->where('curso_acr',$this->acronimo)->where('classe','13ª')->where('disciplina_id',$disciplina->disciplina_id)->first();                  

                  $disciplina->carga_10 = $carga_10 == null ? '-' : $carga_10->carga;
                  $disciplina->carga_11 = $carga_11 == null ? '-' : $carga_11->carga;
                  $disciplina->carga_12 = $carga_12 == null ? '-' : $carga_12->carga;
                  $disciplina->carga_13 = $carga_13 == null ? '-' : $carga_13->carga;                              
                  
          }
         
            return $listaModelo;

   }


    public function decidir_o_ano_terminal_da_disciplina($disciplina_id){
          $user = auth()->user();
          $ano = date('Y');

          $curso_disciplinas = $this->disciplinas(100);
          $curso_disciplina = $curso_disciplinas->where('disciplina_id',$disciplina_id)->first();
          
          $data['terminal'] = 'N';
          $data['disciplina_id'] = $disciplina_id;
          $data['user_id'] = $user->id;          

          $this->criar_modulos_senao_existir();

          $modulo_10 = Modulo::where('nome',$this->acronimo . ' 10ª')->get()->first();
          $modulo_11 = Modulo::where('nome',$this->acronimo . ' 11ª')->get()->first();
          $modulo_12 = Modulo::where('nome',$this->acronimo . ' 12ª')->get()->first();
          $modulo_13 = Modulo::where('nome',$this->acronimo . ' 13ª')->get()->first();
          
          
          $data['modulo_id'] = $modulo_10->id;
          $modulo_10->disciplinas()->updateExistingPivot($disciplina_id,$data);

          $data['modulo_id'] = $modulo_11->id;
          $modulo_11->disciplinas()->updateExistingPivot($disciplina_id,$data);

          $data['modulo_id'] = $modulo_12->id;
          $modulo_12->disciplinas()->updateExistingPivot($disciplina_id,$data);
          $data['modulo_id'] = $modulo_13->id;
         
          if(isset($curso_disciplina->carga_13) && $curso_disciplina->carga_13 != '-'){
             $data['terminal'] = 'S';             
             $data['modulo_id'] = $modulo_13->id;
             $modulo_13->disciplinas()->updateExistingPivot($disciplina_id,$data);

          }else if(isset($curso_disciplina->carga_12) && $curso_disciplina->carga_12 != '-'){
             $data['terminal'] = 'S';             
             $data['modulo_id'] = $modulo_12->id;
             $modulo_12->disciplinas()->updateExistingPivot($disciplina_id,$data);

          }else if(isset($curso_disciplina->carga_11) && $curso_disciplina->carga_11 != '-'){
             $data['terminal'] = 'S';             
             $data['modulo_id'] = $modulo_11->id;
             $modulo_11->disciplinas()->updateExistingPivot($disciplina_id,$data);

          }else if(isset($curso_disciplina->carga_10) && $curso_disciplina->carga_10 != '-'){
             $data['terminal'] = 'S';             
             $data['modulo_id'] = $modulo_10->id;
             $modulo_10->disciplinas()->updateExistingPivot($disciplina_id,$data);
          }
        
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


    public function criar_modulos_senao_existir(){
          
          $user = auth()->user();
          $ano = date('Y');

          $modulo_10 = Modulo::where('nome',$this->acronimo . ' 10ª')->get()->first();
          $modulo_11 = Modulo::where('nome',$this->acronimo . ' 11ª')->get()->first();
          $modulo_12 = Modulo::where('nome',$this->acronimo . ' 12ª')->get()->first();
          $modulo_13 = Modulo::where('nome',$this->acronimo . ' 13ª')->get()->first();

          if(is_null($modulo_10)){
             $classe = Classe::where('nome','10ª')->first();
              $data = [
              'nome'=> $this->acronimo .' '.$classe->nome,
              'curso_id'=> $this->id,              
              'classe_id'=> $classe->id,
              'ano'=> $ano,
              'user_id'=> $user->id
              ];
             $modulo_10 = $this->modulos()->create($data);
             $modulo_10->classe()->associate($classe);
             $modulo_10->user()->associate($user);
             $modulo_10->save();
          }
          if(is_null($modulo_11)){
             $classe = Classe::where('nome','11ª')->first();
             $data = [
              'nome'=> $this->acronimo .' '.$classe->nome,
              'curso_id'=> $this->id,              
              'classe_id'=> $classe->id,
              'ano'=> $ano,
              'user_id'=> $user->id
              ];
              $modulo_11 = $this->modulos()->create($data);
              $modulo_11->classe()->associate($classe);
              $modulo_11->user()->associate($user);
              $modulo_11->save();
          }          

          if(is_null($modulo_12)){
             $classe = Classe::where('nome','12ª')->first();
              $data = [
              'nome'=> $this->acronimo .' '.$classe->nome,
              'curso_id'=> $this->id,              
              'classe_id'=> $classe->id,
              'ano'=> $ano,
              'user_id'=> $user->id
              ];
              $modulo_12 = $this->modulos()->create($data);
              $modulo_12->classe()->associate($classe);
              $modulo_12->user()->associate($user);
              $modulo_12->save();
          }
          if(is_null($modulo_13)){
             $classe = Classe::where('nome','13ª')->first();

               $data = [
              'nome'=> $this->acronimo .' '.$classe->nome,
              'curso_id'=> $this->id,              
              'classe_id'=> $classe->id,
              'ano'=> $ano,
              'user_id'=> $user->id
              ];
                $modulo_13 = $this->modulos()->create($data);
                $modulo_13->classe()->associate($classe);
                $modulo_13->user()->associate($user);
                $modulo_13->save();
          }


    }
}
