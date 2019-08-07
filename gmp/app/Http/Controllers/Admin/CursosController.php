<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Curso;
use App\Area;
use App\Professor;
use App\Disciplina;

class CursosController extends Controller
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
        ["titulo"=>"Cursos","url"=>""]
    ]);
       
       $listaModelo = Curso::listaModelo(30);
       $listaProfessores = Professor::all()->sortBy('nome');
       $listaAreas = Area::all()->sortBy('nome');

       return view('admin.cursos.index',compact('listaMigalhas','listaModelo','user','listaProfessores','listaAreas'));

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
        $validacao = \Validator::make($data,[
        "nome" => "required",        
        "acronimo" => "required",        
        "professor_id" => "required"        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $professor = Professor::find($data['professor_id']);        
        $area = Area::find($data['area_id']);        
        $user = auth()->user();        
        $curso = $user->cursos()->create($data);
        $curso->professor()->associate($professor);
        $curso->area()->associate($area);
        $curso->save();
        $curso->criar_modulos_senao_existir();
        
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
       return Curso::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validacao = \Validator::make($data,[
        "nome" => "required",        
        "acronimo" => "required"        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $professor = Professor::where('nome',$data['professor_id'])->first();
        $area = Area::where('nome',$data['area'])->first();
        $data['professor_id'] = $professor->id;
        $user = auth()->user();
        // $user->cursos()->find($id)->update($data);
        // dd($data);
        $curso = Curso::find($id);
        $curso->update($data);        
        $curso->professor()->associate($professor);        
        $curso->area()->associate($area);
        $curso->user()->associate($user);
        $curso->save();
        // $curso->user()->associate($user);
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
        $curso = Curso::find($id);
        $curso->modulos()->delete();
        $curso->delete();

        return redirect()->back();
    }

    public function disciplinas($curso_id)
    {
        $curso = Curso::find($curso_id);        

        $user = auth()->user();       
       // dd($user);
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Cursos","url"=>route('cursos.index')],
        ["titulo"=>"disciplinas","url"=>""],
    ]);
       // $cabecalho = ['#','Disciplina','Acron.'];
       // if($curso->modulos()->get()->where('nome', $curso->acronimo . ' 10ª')->isNotEmpty()){      
       //  array_push($cabecalho, '10ª');
       // }
       // if($curso->modulos()->get()->where('nome', $curso->acronimo . ' 11ª')->isNotEmpty()){      
       //  array_push($cabecalho, '11ª');
       // }
       // if($curso->modulos()->get()->where('nome', $curso->acronimo . ' 12ª')->isNotEmpty()){      
       //  array_push($cabecalho, '12ª');
       // }
       // if($curso->modulos()->get()->where('nome', $curso->acronimo . ' 13ª')->isNotEmpty()){      
       //  array_push($cabecalho, '13ª');
       // }
       // array_push($cabecalho, 'Curlar');
       // array_push($cabecalho, 'Categoria');
         
       $modulo = $curso->modulos()->get()->first();
       $listaModelo =$curso->disciplinas(100);
       $listaProfessores = Professor::all()->sortBy('nome');
       $listaAreas = Area::all()->sortBy('nome');
       $listaDisciplinas = Disciplina::orderBy('nome')->get();

       return view('admin.cursos.disciplinas.index',compact('listaMigalhas','listaModelo','user','listaProfessores','listaAreas','curso','cabecalho','listaDisciplinas','modulo'));

    }
}
