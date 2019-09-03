<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Curso;
use App\Classe;
use App\Aluno;
use App\Turma;
use App\Epoca;
use App\Instituicao;
use App\Disciplina;

class TurmaAlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   

       $user = auth()->user();       
       $turma = Turma::find($id);      
       $epoca = Epoca::where('activo','S')->first();          

       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Alunos","url"=>""]
    ]);
       
       $listaModelo = Turma::listaAlunos($id,100);                     
       $listaCursos = Curso::all();
       $listaTurmas = Turma::orderBy('nome')->get();
       $modulo = $turma->modulo()->first();
       $listaDisciplinas = Disciplina::orderBy('nome')->get();
       $listaAlunos = Turma::alunos_turmas_actuais($turma->modulo_id,100);
       
          
       $user = auth()->user();
       return view('admin.turmas.alunos.index',compact('turma','listaMigalhas','listaModelo','listaCursos','listaTurmas','listaAlunos','user','modulo','listaDisciplinas'));
    }

    public function listaModelo($id)
    {  
       $turma = Turma::find($id);    
       $listaAlunos = Turma::alunos_turmas_actuais($turma->modulo_id,100);
       return $listaAlunos;
    }    

    
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        $data = $request->except('_token');                
        $user = auth()->user();
        $data['user_id'] = $user->id;
             
            TurmaAlunoController::inscrever_aluno_na_turma($data);
            return redirect()->back();
    }


    public static function inscrever_aluno_na_turma($data){      
        $newData = $data;
        $turma = Turma::find($data['turma_id']);           
       
        foreach($data['aluno_id'] as $aluno_id) {
            $aluno = Aluno::find($aluno_id);
            $newData['aluno_id'] = $aluno_id;              
            $newData['numero'] = Turma::qtdAlunos($data['turma_id']) + 1;
            $newData['provenienca'] = $data['provenienca'];
            if($turma->alunos()->where('aluno_id',$aluno_id)->get()->isEmpty()){
                $turma->alunos()->attach(intVal($aluno_id),$newData);
            }                    

        $aluno->migrarNotasAnteriores();
        }
        // TurmaAlunoController::ordenar_numeros_por_nome($turma,0,0);

    }
    
    public static function ordenar_numeros_por_nome($turma,$aluno_id,$numero){
        $alunos_da_turma = $turma->listaAlunos($turma->id,100);       
        $limite = $numero == 0 ? $alunos_da_turma->count() : $numero;

        if($aluno_id > 0){
           foreach ($alunos_da_turma as $key => $aluno){              
               if($aluno->id == $aluno_id){             
                  unset($alunos_da_turma[$key]);
                  break;
               }              
           }
        }

        $numero = 0; 
        foreach ($alunos_da_turma as $key => $aluno){
             if($numero == $limite-1){                                    
                continue;
             }              
             $numero++;
               $data['numero'] = $numero;                 
               $aluno->numero = $numero;                
               $turma->alunos()->updateExistingPivot($aluno->id,['numero' => $numero]);

        } 
    }
    public function actualizar_num($id)
    {
       $turma = Turma::find($id);
       TurmaAlunoController::ordenar_numeros_por_nome($turma,0,0);
        return redirect()->back();
    }

    public function show($id)
    {
       return Aluno::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       return view('modulos.modulo');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();        
       
               
        $validacao = \Validator::make($data,[           
        "status" => "required",       
        // "duracao" => "required"        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        // dd($data);
            $user = auth()->user();
            $turma = Turma::find($data['turma_id']);
            $aluno = Aluno::find($data['aluno_id']);            
            $turma->alunos()->updateExistingPivot($data['aluno_id'],$request->only(['numero','cargo','provenienca','status']));
            $aluno->status = $data['status'];
            $turma->user()->associate($user);
            $aluno->user()->associate($user);
            $turma->save();
            $aluno->save();
            // TurmaAlunoController::ordenar_numeros_por_nome($turma,$aluno->id,$data['numero']);

        
   
        return redirect()->back();
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($turma_id,$aluno_id)
    {         
           $aluno = Aluno::find($aluno_id);               
           $turma = Turma::find($turma_id);
           $turma_anterior = $aluno->buscarTurmaAnterior($turma);
           $aluno->revalidarNotasAnteriores($turma_anterior);          
           $aluno->avaliacaos()->where('turma_id',$turma_id)->delete();           
           $turma->alunos()->detach($aluno_id);
           // TurmaAlunoController::ordenar_numeros_por_nome($turma,0,0);
            return redirect()->back();
            // return 204;                
    }
    public function deleteMultiple($turma_id,$aluno_id)
    {   
       // if(is_array($turma_id)){
           // $aluno = Aluno::find($aluno_id);      
           // $aluno->avaliacaos()->where('turma_id',$turma_id)->delete();
           // Turma::find($turma_id)->alunos()->detach($aluno_id);
           //  return 204;            
       // }else{
       //     $aluno = Aluno::find($aluno_id);      
       //     $aluno->avaliacaos()->where('turma_id',$turma_id)->delete();
       //     Turma::find($turma_id)->alunos()->detach($aluno_id);
       //      return redirect()->back();       
       // }
    }
}
