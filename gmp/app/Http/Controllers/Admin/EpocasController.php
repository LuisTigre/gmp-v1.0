<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Atividade;
use App\Epoca;
use App\Turma;
use App\Aluno;
use App\Modulo;

class EpocasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaMigalhas = json_encode([
        ["titulo"=>"Home","url"=>route('admin')],
        ["titulo"=>" Anos Acadêmicos","url"=>""]
    ]);
        $listaModelo = Epoca::listaModelo(20);       
        $user = auth()->user();        
        return view('admin.epocas.index',compact('listaMigalhas','listaModelo','user'));
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
         'ano_lectivo' => 'required|unique:epocas' 
        // "data_final" => "required"
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();            
        Epoca::create($data);
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
        
        set_time_limit(240);
        $data = $request->all();
        $validacao = \Validator::make($data,[
        // 'ano_lectivo' => 'required|unique:epocas'        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();
        $epoca = Epoca::find($id);
        
        if($data['activo'] == 'S'){
           $epocasAtivas = Epoca::where('activo','S')->get();
           foreach ($epocasAtivas as $value) {
               $value->activo = 'N';
               $value->save();
           }
        }

        $epoca->update($data);
        $epoca->user()->associate($user);
        $epoca->save();
        if($epoca->fechado == 'S'){
          $data['fechado'] = 'S';          
          $turmas = Turma::where('ano_lectivo',$epoca->ano_lectivo)->get();
          foreach ($turmas as $turma) {
               $alunos = $turma->alunos()->get();
               if(sizeof($alunos) != 0){
                $avaliacoes = Turma::classificaoAnual($turma->id,'III');
                foreach ($avaliacoes['data'] as $avaliacao) {                   
                    
                   if($avaliacao['Result'] == 'Trans.'){
                        $aluno = Aluno::where('idmatricula',$avaliacao['idmatricula'])->first();
                        $turma_nome = explode(' ', $turma->nome);
                        $modulo = '';
                        if($turma_nome[1] =='10ª'){                                          
                            $modulo = Modulo::where('nome',$turma_nome[0] . ' 11ª')->first();
                            $aluno->modulo_id = $modulo->id;
                            $aluno->save();                        
                        }else if($turma_nome[1] =='11ª'){
                            $modulo = Modulo::where('nome',$turma_nome[0] . ' 12ª')->first();
                            $aluno->modulo_id = $modulo->id;
                            $aluno->save();
                        }else{
                            $modulo = Modulo::where('nome',$turma_nome[0] . ' 13ª')->first();
                            $aluno->modulo_id = $modulo->id;
                            $aluno->save();
                        }                      
                   }
                }
               }
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
    public function destroy($id)
    {
        Epoca::find($id)->delete();
        return redirect()->back();
    }
}
