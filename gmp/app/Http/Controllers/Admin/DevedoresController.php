<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Aluno;
use Illuminate\Validation\Rule;

class DevedoresController extends Controller
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
        ["titulo"=>"Lista de Devedores","url"=>""]
        ]);
      // dd('got it');
       $listaModelo = Aluno::select('id','nome','encarregado_tel','telefone')->where('devedor','=','S')->paginate(5);
       $listaAlunos = Aluno::listaAlunos(100);      
       return view('admin.alunos.devedores.index',compact('listaMigalhas','listaModelo','listaAlunos'));
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
        'aluno_id' => 'required'       
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        foreach ($data['aluno_id'] as $key => $value) {
             $aluno = Aluno::find($value);
             $aluno->devedor = 'S';
             $aluno->save();
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
        return User::find($id);
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
        'aluno_id' => 'required'       
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        foreach ($data['aluno_id'] as $key => $value) {
             $aluno = Aluno::find($value);
             $aluno->devedor = 'N';
             $aluno->save();
        }       

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
        dd('Hey it worked');
        $aluno = Aluno::find($id);
        $aluno->devedor = 'N';
        $aluno->update();
        return redirect()->back();
    }
}
