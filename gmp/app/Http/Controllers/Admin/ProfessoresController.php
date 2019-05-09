<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\ProfessorsImport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Professor;
use App\User;
use Illuminate\Validation\Rule;

class ProfessoresController extends Controller
{
    

public function index()
    {
    $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Lista de Professores","url"=>""]
    ]);
       $user = auth()->user();       
       $listaModelo = Professor::listaProfessores(100);

       return view('admin.professores.index',compact('listaMigalhas','listaModelo','user'));

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
        'nome' => 'required|string|max:255',        
        'telefone' => 'required|string|max:9|min:9',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',       
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }

        $user = auth()->user();
        $data['password'] = bcrypt($data['password']);
        $data['name'] = $data['nome'];
        unset($data['nome']);        
        User::create($data);

        unset($data['password']);
        $data['nome'] = $data['name'];
        unset($data['name'],$data['professor']);
        $professor = Professor::create($data);
        $professor->user()->associate($user);
        $professor->save();
        return redirect()->back();

      }else if($request->has('excel_file')){
        Excel::import(new ProfessorsImport,request()->file('excel_file'));
        return redirect()->route('professores.index');
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
        return Professor::find($id);
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
       if(isset($data['password']) && $data['password'] != ""){
            $validacao = \Validator::make($data,[
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            ]);
            $data['password'] = bcrypt($data['password']);       
        }else{
            $validacao = \Validator::make($data,[
            'nome' => 'required|string|max:255',                      
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($id)]           
            ]);
            unset($data['password']);            
        }

        $user = auth()->user();
        $data['name'] = $data['nome'];
        unset($data['nome']);  
        $userAcount = User::where('email',$data['email'])->first();
        $userAcount->update($data);
        $data['nome'] = $data['name'];
        unset($data['name'],$data['professor']);        
        $professor = Professor::find($id);
        $professor->update($data); 
        $professor->user()->associate($user);
        $professor->save();
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
        $professor = Professor::find($id);
        $user = User::where('email',$professor->email)->first();
        $professor->delete();
        $user->delete();
        return redirect()->back();
    }

    public function fileUpload(){
        return view('admin.professores.upload');
   }
}
