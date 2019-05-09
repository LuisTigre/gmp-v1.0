<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Atividade;
use App\AtividadeGrupo;
use App\Epoca;

class AtividadesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Lista de Atividades","url"=>""]
    ]);
       
       $listaModelo = Atividade::listaModelo(20);
       $listaGrupos = AtividadeGrupo::all();
       $user = auth()->user();

       return view('admin.atividades.index',compact('listaMigalhas','listaModelo','listaGrupos','user'));

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
        $validacao = \Validator::make($data,[
        // "nome" => "required",
        // "grupo" => "required",
        // "trimestre" => "required",
        // "prazo_inicial" => "required",
        // "prazo_final" => "required"
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }

        $user = auth()->user();         
        $epoca = Epoca::where('trimestre',$data['trimestre'])->first();
        $grupo = AtividadeGrupo::find($data['grupo']);

        
        $atividade = $user->atividades()->create($data);
        $atividade->grupo()->associate($grupo);
        $atividade->epoca()->associate($epoca);        
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
       return Atividade::find($id);
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
        $validacao = \Validator::make($data,[
        "titulo" => "required",
        "descricao" => "required",
        "conteudo" => "required",
        "data" => "required"
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();
        $user->atividades()->find($id)->update($data);
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
        Atividade::find($id)->delete();
        return redirect()->back();
    }
}
