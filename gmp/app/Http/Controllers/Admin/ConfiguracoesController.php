<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Atividade;
use App\AtividadeGrupo;
use App\Epoca;

class ConfiguracoesController extends Controller
{
    public function index()
    {
        $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Lista de Epocas","url"=>""]
    ]);
        $listaModelo = Epoca::listaModelo(20);       
        $user = auth()->user();

        return view('admin.configuracoes.index',compact('listaMigalhas','listaModelo','user'));
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
        "nome" => "required"
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();
        dd($data);        
        $user->epocas()->create($data);
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
       return Epoca::find($id);
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
        // "titulo" => "required",
        // "descricao" => "required",
        // "conteudo" => "required",
        // "data" => "required"
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $epoca = Epoca::find($id);
        dd($epoca);
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
        Epoca::find($id)->delete();
        return redirect()->back();
    }
}
