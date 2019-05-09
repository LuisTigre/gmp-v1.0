<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Professor;
use App\Turma;


class TurmaProfessorController extends Controller
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
       $modulo = $turma->modulo()->get();
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Professores","url"=>""]
    ]);
       
       $listaModelo = Turma::listaProfessores($id,40);          
       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();

       

       return view('admin.turmas.professors.index',compact('turma','listaMigalhas','listaModelo','listadisciplinas','listaProfessores','user'));

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
            $turma = Turma::find($data['turma_id'])->professores()->attach($data['professor_id'],$data);
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
    public function show($turmaId,$ProfessorId)
    {
       // return Modulo::find($moduloId,$ProfessorId);
        return view('admin.turmas.professors.index');
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
       
        // dd($data);        
        // $validacao = \Validator::make($data,[
        // "Professor" => "required",        
        // "carga" => "required",       
        // "duracao" => "required"        
        // ]);
        // if($validacao->fails()){
        //     return redirect()->back()->withErrors($validacao)->withInput();
        // }
        // dd($data);
        
        $user = auth()->user();            
            $turma = Turma::find($data['turma_id'])->professores()->updateExistingPivot($data['Professor_id'],$request->only(['numero','cargo']));

            // $turma->attach($user);
        
   
        return redirect()->back();
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($turmaId,$professorId)
    {

       Turma::find($turmaId)->professores()->detach($professorId);
        return redirect()->back();
    }
}
