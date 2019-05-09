<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Professor;
use App\Turma;
use App\Aula;
use App\Sala;
use App\Tempo;


class AulasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($turma_id)
    {  

       $user = auth()->user();       
       $turma = Turma::find($turma_id);      
       $modulo = $turma->modulo()->get();
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Aulas","url"=>""]
    ]);
       
       $listaModelo = Aula::listaModelo($turma_id,50); 
                 
       $listaDisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();
       $salas = Sala::all()->sortBy('nome'); 
       
       $user = auth()->user();

       

       return view('admin.turmas.aulas.index',compact('turma','listaMigalhas','listaModelo','listaProfessores','listaDisciplinas','user','salas'));

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
            // dd($data);
            $user = auth()->user();
            $data['user_id'] = $user->id;  
            // dd($data);    
            $turma = Turma::find($data['turma_id'])->disciplinas()->attach($data['professor_id'],$data);
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
              
       return Aula::find($id);
       
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
        
        if(!$data['single_update']){       
        $validacao = \Validator::make($data,[
        "sala_id" => "required"               
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }        
        $user = auth()->user();
        
        $aula = Aula::find($id);
        $sala = Sala::find($data['sala_id']);        
        $aula->sala()->associate($sala);
        $aula->user()->associate($user);
        $aula->save();        
   
        return redirect()->back();
       }else{
        $validacao = \Validator::make($data,[
        "tempo_id" => "required"               
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }        
        $user = auth()->user();
        $aula = Aula::find($id);
        $tempo_info = explode('_', $data['tempo_id']);
        $tempo = Tempo::where('dia',$tempo_info[0])->get()->where('hora',$tempo_info[1])->first();      
        $aula->tempo()->associate($tempo);
        $aula->user()->associate($user);
        $aula->save();   
        return response(null, Response::HTTP_OK);
       }
    }

    public function update_tempo_id($aula_id)
    {
            
            dd($request);
            $user = auth()->user();
            $aula->tempo()->associate($tempo);
            $aula->user()->associate($user);
            $aula->update(['descricao'=>'Best lesson ever']);            
        
        return $aula->save();        
    }

    public function atribuirTempo($aula_id,$tempo_id)
    {
        $user = auth()->user();
        $tempo_info = explode('_', $tempo_id);
        $aula = Aula::find($aula_id);
        $turma = Turma::find($aula->turma_id);

        // $aulas = $turma->aulas()->where($tempo_id)->get();
        $tempo = Tempo::where('dia',$tempo_info[0])->get()->where('hora',$tempo_info[1])->first();
        $aulas = $turma->aulas()->where('tempo_id',$tempo->id)->get();
        $data = ['aula'=>$aula,'tempo'=>$tempo];
        
        return $data;        
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($aula)
    {
        dd($aula);
       // Turma::find($turmaId)->disciplinas()->detach($professorId);

        return redirect()->back();
    }
}
