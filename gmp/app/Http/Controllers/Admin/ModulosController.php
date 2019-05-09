<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Curso;
use App\Classe;
use App\Disciplina;
use App\Epoca;

class ModulosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $user = auth()->user();       
       // dd($user);
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Modulos","url"=>""]
    ]);
       
       $listaModelo = Modulo::listaModelo(15);      
       $listaDisciplinas = Modulo::listaDisciplinas(1,5);
       // dd($listaDisciplinas);    
       $listaCursos = Curso::all();
       $listaClasses = Classe::all();
       // $modulo = Modulo::find(1);
       // foreach ($modulo->disciplinas as $disciplina) {
       //     dd($disciplina->pivot);
       // }

       

       return view('admin.modulos.index',compact('listaMigalhas','listaModelo','listaCursos','listaClasses','user','listaDisciplinas'));

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all(); 
        // dd($data);       
        if(!isset($data['disciplina_id'])){
            $epoca = Epoca::where('activo','S')->first();            
            $data['ano'] = $epoca->ano_lectivo;
            $curso = Curso::find($data['curso']);
            $classe = Classe::find($data['classe']);
            $data['nome'] = $curso->acronimo . ' ' . $classe->nome;
            $validacao = \Validator::make($data,[            
            "nome" => "required|string|unique:modulos",        
            "curso" => "required",        
            "classe" => "required"                   
            ]);
            if($validacao->fails()){
                return redirect()->back()->withErrors($validacao)->withInput();
            }
            $user = auth()->user();
            $modulo = Modulo::where('curso_id',$curso->id)->get()
                            ->where('curso_id',$classe->id)
                            ->where('ano',$epoca->ano_lectivo);
            
            if($modulo->isEmpty()){
                $modulo = $curso->modulos()->create($data);
                $modulo->classe()->associate($classe);
                $modulo->user()->associate($user);
                $modulo->save();
            }                            
            
        }else{
            // dd($data);               
            $validacao = \Validator::make($data,[
            "carga" => "required",        
            "duracao" => "required"      
            ]);
            if($validacao->fails()){
                return redirect()->back()->withErrors($validacao)->withInput();
            }
            $user = auth()->user();            
            Modulo::find($data['modulo_id'])->disciplinas()->attach($data['disciplina_id'],$request->only(['carga','duracao']));
        }
       
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       return Modulo::find($id);
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
        "curso" => "required",        
        "classe" => "required"        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();
        $epoca = Epoca::where('activo','S')->first();        
        $data['ano'] = $epoca->ano_lectivo;
        // $user->cursos()->find($id)->update($data);
        Modulo::find($id)->update($data);
    
        return redirect()->back();
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
       // dd($id);
        Modulo::find($id)->delete();
        return redirect()->back();
    }
}
