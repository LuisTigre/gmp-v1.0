<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modulo;
use App\Curso;
use App\Classe;
use App\Disciplina;
use App\Turma;
use App\Aula;

class ModuloDisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {       
       $user = auth()->user();       
       $modulo = Modulo::find($id);

       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Modulos","url"=>route('modulos.index')],       
        ["titulo"=>"Disciplinas","url"=>""]
    ]);
       
       $listaModelo = Modulo::listaDisciplinas($id,14);
       $listaCursos = Curso::all();
       $listaClasses = Classe::all();
       $listaDisciplinas = Disciplina::orderBy('nome')->get();
       $user = auth()->user();

       

       return view('admin.modulos.disciplinas.index',compact('modulo','listaMigalhas','listaModelo','listaCursos','listaClasses','listaDisciplinas','user'));

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
        $data = $request->all();        
        
                          
            $validacao = \Validator::make($data,[
            "carga" => "required"            
            ]);
            if($validacao->fails()){
                return redirect()->back()->withErrors($validacao)->withInput();
            }
            $user = auth()->user(); 
            $modulo = Modulo::find($data['modulo_id']);
            $disciplina = Disciplina::find($data['disciplina_id']); 
            $modulo->disciplinas()->attach($data['disciplina_id'],$request->only(['carga','terminal','do_curso']));
            $turmas = $modulo->turmas()->get();
            foreach ($turmas as $key=>$turma) {
                $turma->disciplinas()->attach($disciplina);
            }
            // $turmas->saveMany($disciplina);
            // disciplinas()->saveMany($disciplina);
            // $turma->disciplinas()->saveMany($disciplinas);
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
    public function show($moduloId,$disciplinaId)
    {
       
       return Modulo::find($moduloId,$disciplinaId);
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
        "disciplina" => "required",        
        "carga" => "required"                
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $modulo = modulo::find($id);
        $disciplina = Disciplina::where('nome','=',$data['disciplina'])->first();        
        $data['disciplina'] = $disciplina->id;
        
        $user = auth()->user();
            
        $updated_data = ['disciplina_id'=> $data['disciplina'],
                         'disciplina_modulo.user_id'=>$user->id,
                         'carga'=> $data['carga'],
                         'terminal'=> $data['terminal'],                            
                         'do_curso'=> $data['do_curso'],
                         'curricular'=> $data['curricular']
                     ];                            
        $modulo->disciplinas()->updateExistingPivot($data['disciplina'],$updated_data);
        
        $turmas = $modulo->turmas()->get();        

        foreach ($turmas as $turma) {            
            $aulas = $turma->aulas()->where('disciplina_id',$disciplina->id)->get();          
            $diferenca = $aulas->count()-intVal($data['carga']);


            if($diferenca > 0){                
                for ($i=$diferenca; $i > 0; $i--) {
                    $aula = $aulas->last();                      
                    $aulas->last()->delete();               
                }
            }else if($diferenca < 0){               
            //     for ($i=$diferenca; $i < 0; $i++) {
            //         $aula = $aulas->last();
                                 
            // $newAula = new Aula(['nome' => 'aula',
            //                      'disciplina_id'=>$aula->disciplina_id,
            //                      'sala_id'=>$aula->sala_id,
            //                      'turma_id'=>$aula->turma_id,
            //                      'professor_id'=>$aula->professor_id
            //     ]);
                  
            // $newAula->user()->associate($user);
            // $newAula->save();
            //     }
            }
        }
        
       
        return redirect()->back();
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduloId,$disciplinaId)
    {
       $modulo = Modulo::find($moduloId);
       $modulo->disciplinas()->detach($disciplinaId);

       $turmas = $modulo->turmas()->get();
            foreach ($turmas as $key=>$turma) {
                $turma->disciplinas()->detach($disciplinaId);
            }
        return redirect()->back();
    }
}
