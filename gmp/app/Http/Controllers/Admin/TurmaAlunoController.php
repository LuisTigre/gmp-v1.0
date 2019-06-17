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

       $listaAlunos = Turma::alunos_turmas_actuais($turma->modulo_id,100);
       
          
       $user = auth()->user();
       return view('admin.turmas.alunos.index',compact('turma','listaMigalhas','listaModelo','listaCursos','listaTurmas','listaAlunos','user','modulo'));
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
             
            // dd($data);               
            // $validacao = \Validator::make($data,[
            // "carga" => "required",        
            // "duracao" => "required"      
            // ]);
            // if($validacao->fails()){
            //     return redirect()->back()->withErrors($validacao)->withInput();
            // }
            $user = auth()->user();
            $data['user_id'] = $user->id;
            $newData = $data;
            $turma = Turma::find($data['turma_id']);           
            $aluno = Aluno::find($data['aluno_id']);

            foreach($data['aluno_id'] as $aluno) {
                $newData['aluno_id'] = $aluno;              
                $newData['numero'] = Turma::qtdAlunos($data['turma_id']) + 1;                            
                $turma->alunos()->attach(intVal($aluno),$newData);

            }
            $alunos_da_turma = $turma->listaAlunos($turma->id,100);            
            // $collator = collator('en_us');
            // $alunos_da_turma = $collator->sort($alunos_da_turma);
            $numero = 0;
            foreach ($alunos_da_turma as $key => $aluno_info){                
                 $aluno = Aluno::find($aluno_info->id);
                 $numero++;
                 $data['numero'] = $numero;                 
                 $aluno_info->numero = $numero;                
                 $turma->alunos()->updateExistingPivot($aluno_info->id,['numero' => $numero]);
                 $aluno->migrarNotasAnteriores();                 
                }           
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
            $turma->alunos()->updateExistingPivot($data['aluno_id'],$request->only(['numero','cargo','status']));
            $aluno->status = $data['status'];
            $turma->user()->associate($user);
            $aluno->user()->associate($user);
            $turma->save();
            $aluno->save();

        
   
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
           $aluno->avaliacaos()->where('turma_id',$turma_id)->delete();
           Turma::find($turma_id)->alunos()->detach($aluno_id);
            // return redirect()->back();
            return 204;                
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
