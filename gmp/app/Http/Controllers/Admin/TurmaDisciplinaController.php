<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Professor;
use App\Turma;


class TurmaDisciplinaController extends Controller
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
        ["titulo"=>"Disciplinas e Professores","url"=>""]
    ]);
       
       $listaModelo = Turma::listaDisciplinas($turma_id,14);          
       $listaDisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();

       

       return view('admin.turmas.disciplinas.index',compact('turma','listaMigalhas','listaModelo','listaProfessores','listaDisciplinas','user'));

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

       return Disciplina::find($id);
       
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

        $disciplina = Disciplina::find($data['disciplina_id']);       
        $data['user_id'] = $user->id; 
            $turma = Turma::find($data['turma_id']);            
            $professor = Professor::where('nome',$data['professor'])->first();
            // dd($professor);
            $updated_data = [
                         'disciplina_id'=> $disciplina->id,
                         'disciplina_turma.user_id'=>$user->id,
                         'professor_id'=>$professor->id,
                         'director'=>$data['director']
                         
                        ];  
            $turma->disciplinas()->updateExistingPivot($data['disciplina_id'],$updated_data);
            // $turma->professores()->attach($professor,$updated_data);
            
             // $aulas = $turma->aulas()->where('disciplina_id',$disciplina->id)->get(); 
             // foreach ($aulas as $aula) {
             //    $aula->professor()->associate($professor->id);
             //    $aula->save();
             // }
   
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

       Turma::find($turmaId)->disciplinas()->detach($professorId);
        return redirect()->back();
    }
}
