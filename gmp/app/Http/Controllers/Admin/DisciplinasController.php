<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\DisciplinasImport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Disciplina;
use App\Modulo;

class DisciplinasController extends Controller
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
        ["titulo"=>"Disciplinas","url"=>""]
    ]);
       /*$listaModelo = curso::select('id','titulo','descricao','user_id','data')->paginate(5);
       foreach ($listaModelo as $key => $value) {
           $value->user_id = \App\User::find($value->user_id)->name;
           // $value->user_id = $value->user->name;
           unset($value->user); 
       }*/
       /*$listaModelo = DB::table('cursos')
                       ->join('users','users.id','=','cursos.user_id')
                       ->select('cursos.id','cursos.titulo','cursos.descricao','users.name','cursos.data')
                       ->whereNull('deleted_at')
                       ->paginate(5);*/
       $listaModelo = Disciplina::listaModelo(50);

       return view('admin.disciplinas.index',compact('listaMigalhas','listaModelo','user'));

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
       if($request->has('nome')){        
        $validacao = \Validator::make($data,[
        "nome" => "required|unique:disciplinas",        
        "acronimo" => "required|unique:disciplinas"        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();        
        $user->disciplinas()->create($data);
        return redirect()->back();

       }else if($request->has('excel_file')){
        Excel::import(new DisciplinasImport,request()->file('excel_file')); 
        return redirect()->route('disciplinas.index');
       }else{
        return redirect()->back();
       }
    }

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
        if(!isset($data['modulo_id'])){
            // dd($data);        
            $validacao = \Validator::make($data,[
            "nome" => "required",        
            "acronimo" => "required"        
            ]);
            if($validacao->fails()){
                return redirect()->back()->withErrors($validacao)->withInput();
            }
            $user = auth()->user();
            // $user->cursos()->find($id)->update($data);
            Disciplina::find($id)->update($data);
        }else{
       
         // dd($data);        
        $validacao = \Validator::make($data,[
        "disciplina" => "required",        
        "carga" => "required",       
        "duracao" => "required"        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        
        $user = auth()->user();
            
        $updated_data = ['disciplina_id'=> $data['disciplina_id'],
                         'disciplina_modulo.user_id'=>$user->id,
                         'carga'=> $data['carga'],
                         'duracao'=> $data['duracao']];                            
        Modulo::find($data['modulo_id'])->disciplinas()->updateExistingPivot($data['disciplina_id'],$updated_data);
        
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
        Disciplina::find($id)->delete();
        return redirect()->back();
    }

    public function fileUpload(){
        return view('admin.disciplinas.upload');
    }
}
