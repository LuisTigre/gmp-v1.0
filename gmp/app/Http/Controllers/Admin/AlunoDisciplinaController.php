<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Professor;
use App\Turma;


class AlunoDisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($turma_id,$disciplina_id)
    {  

       $user = auth()->user();       
       $turma = Turma::find($turma_id);      
       $disciplina = Disciplina::find($disciplina_id);
       // $alunos = Turma::listaAlunosRepOuNao($turma->id,100);
       // $alunos_rep = $alunos->where('repetente','S');

       // $alunos_sorted = $alunos->where('repetente','N');       
       // $alunos_sorted_arr = [];       
       // $data = [];
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Alunos","url"=>""]
    ]);

       // foreach ($alunos_sorted as $alunos_sorted) {
       //          $data = ["id"=>$alunos_sorted->id,
       //        "idmatricula"=>$alunos_sorted->idmatricula,
       //        "numero"=>$alunos_sorted->numero,
       //        "nome"=>$alunos_sorted->nome,
       //        "idade"=>$alunos_sorted->idade,
       //        "sexo"=>$alunos_sorted->sexo,
       //        "status"=>$alunos_sorted->status,
       //        "repetente"=>$alunos_sorted->repetente,
       //        "usuario"=>$alunos_sorted->usuario];

       //        array_push($alunos_sorted_arr, $data);          
       // }
       // foreach ($alunos_rep as $repetente){       
       
       //     $avaliacaoDaDisciplina = Turma::avaliacoesDoAluno2($repetente->id,'S')->where('disciplina_id',$disciplina_id)->first();           
       //     if(isset($avaliacaoDaDisciplina['result']) 
       //      && ($avaliacaoDaDisciplina['result'] == 'Tran.' 
       //      || $avaliacaoDaDisciplina['result'] == 'Continua')){            
       //     }else{

       //       $data = ["id"=>$repetente->id,
       //        "idmatricula"=>$repetente->idmatricula,
       //        "numero"=>$repetente->numero,
       //        "nome"=>$repetente->nome,
       //        "idade"=>$repetente->idade,
       //        "sexo"=>$repetente->sexo,
       //        "status"=>$repetente->status,
       //        "repetente"=>$repetente->repetente,
       //        "usuario"=>$repetente->usuario];               
       //       array_push($alunos_sorted_arr, $data);
       //     }
       // }
      
       $listaModelo = $disciplina->buscarAlunosDaDisciplina($turma); 
       // dd($listaModelo); 
       $listaDisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();

       

       return view('admin.turmas.disciplinas.alunos.index',compact('turma','listaMigalhas','listaModelo','listaProfessores','listaDisciplinas','disciplina','user'));

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
                         'professor_id'=>$professor->id
                         
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
