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

       $listaAlunos = Aluno::where('modulo_id',$modulo->id)->get()->sortBy(['data','nome']);
       $listaAlunos2 = collect([]);

       $alunos_da_turma = collect([]);
       $alunos_filtrados = collect([]);
       $turmas_do_ano = Turma::where('ano_lectivo',$epoca->ano_lectivo)->get();

       
       foreach ($listaAlunos as $aluno) {

            foreach ($turmas_do_ano as $turma_do_ano) {
                $alunos_da_turma = $turma_do_ano->alunos()->where('id',$aluno->id)->get();
            }
               if(sizeof($alunos_da_turma) == 0){                
                $alunos_filtrados->prepend($aluno);                
               }           
           }
       $listaAlunos2 = $alunos_filtrados;
             
       $listaAlunos = $listaAlunos2->where('modulo_id',$modulo->id)->sortByDesc('data_de_nascimento');
          
       $user = auth()->user();
       return view('admin.turmas.alunos.index',compact('turma','listaMigalhas','listaModelo','listaCursos','listaTurmas','listaAlunos','user','modulo'));
    }    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        return redirect()->back();
    }
}
