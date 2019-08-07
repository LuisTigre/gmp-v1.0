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
            $curso = $modulo->curso;
            $disciplina = Disciplina::find($data['disciplina_id']);            
            $disciplinas = $curso->disciplinas(100);
            
            foreach ($disciplina as $key => $disc) {              
                if($disciplinas->where('disciplina_id',$disc->id)->isNotEmpty()){
                    unset($disciplina[$key]);
                }
            }            
            $data['disciplina_id'] = $disciplina;            
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
        // dd($data);       
        $user = auth()->user();
        $validacao = \Validator::make($data,[
        "disciplina" => "required"                        
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
       
        $modulo = Modulo::find($data['modulo_id']);
        $curso = $modulo->curso;

        $modulos = $curso->modulos()->get();
        $disciplina = Disciplina::where('nome','=',$data['disciplina'])->first(); 
        $curso->decidir_o_ano_terminal_da_disciplina($disciplina->id);
        $data['disciplina_id'] = $disciplina->id;       
        $data['user_id'] = $user->id;        
        unset($data['disciplina']);

        if((isset($data['carga_10']) && isset($data['carga_12'])) &&   !isset($data['carga_11'])){
            $data['carga_11'] = $data['carga_10'];
        }

        foreach ($modulos as $key => $modulo) {
            $classe = $modulo->classe;
                
            
            $new_data = $this->organizar_os_dados_da_disciplina_do_modulo($modulo,$disciplina,$data,10);           
            if(!empty($new_data)){
                $this->actualizar_disciplina_do_modulo($modulo,$disciplina,$new_data);
                $this->actualizar_disciplinas_das_turmas_do_modulo($modulo,$disciplina,$new_data);
            }

            $new_data = $this->organizar_os_dados_da_disciplina_do_modulo($modulo,$disciplina,$data,11);           
            if(!empty($new_data)){
                $this->actualizar_disciplina_do_modulo($modulo,$disciplina,$new_data);
                $this->actualizar_disciplinas_das_turmas_do_modulo($modulo,$disciplina,$new_data);
            }

            $new_data = $this->organizar_os_dados_da_disciplina_do_modulo($modulo,$disciplina,$data,12);           
            if(!empty($new_data)){
                $this->actualizar_disciplina_do_modulo($modulo,$disciplina,$new_data);
                $this->actualizar_disciplinas_das_turmas_do_modulo($modulo,$disciplina,$new_data);
            }

            $new_data = $this->organizar_os_dados_da_disciplina_do_modulo($modulo,$disciplina,$data,13);           
            if(!empty($new_data)){
                $this->actualizar_disciplina_do_modulo($modulo,$disciplina,$new_data);
                $this->actualizar_disciplinas_das_turmas_do_modulo($modulo,$disciplina,$new_data);
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

    public function organizar_os_dados_da_disciplina_do_modulo($modulo,$disciplina,$data,$clas){
              $classe = $modulo->classe;
              $user = auth()->user();
              $new_data = [];               
            if($classe->nome == $clas.'ª'){
                if(isset($data['carga_'.$clas])){                    
                    $new_data['carga'] = $data['carga_'.$clas];
                    $new_data['disciplina_id'] = $disciplina->id;
                    $new_data['user_id'] = $user->id;
                    $new_data['curricular'] = $data['curricular'];
                    $new_data['modulo_id'] = $modulo->id;
                }else{
                    $this->destroy($modulo->id,$disciplina->id);
                }
            }
                return $new_data;
            
    }       
    public function actualizar_disciplina_do_modulo($modulo,$disciplina,$data){       
       
            $modulo_disciplinas = $modulo->disciplinas()->get();
            $nao_existe = $modulo_disciplinas->where('id',$disciplina->id)->isEmpty();
              if($nao_existe){
                    $modulo->disciplinas()->attach($disciplina->id);              
                    $modulo->disciplinas()->updateExistingPivot($disciplina->id,$data);                            
              }else{
                    $modulo->disciplinas()->updateExistingPivot($disciplina->id,$data);                    
              }            
                 
                      
    }

    public function actualizar_disciplinas_das_turmas_do_modulo($modulo,$disciplina,$data){             
        $turmas = $modulo->turmas()->get();

        foreach ($turmas as $key=>$turma) {
            $turma_disciplinas = $turma->disciplinas()->get();
            $nao_existe = $turma_disciplinas->where('id',$disciplina->id)->isEmpty();
              if($nao_existe){
                    $turma->disciplinas()->attach($disciplina->id);              
              }            
        }       
            // foreach ($turmas as $turma) {            
            //     $aulas = $turma->aulas()->where('disciplina_id',$disciplina->id)->get();          
            //     $diferenca = $aulas->count()-intVal($data['carga']);


            //     if($diferenca > 0){                
            //         for ($i=$diferenca; $i > 0; $i--) {
            //             $aula = $aulas->last();                      
            //             $aulas->last()->delete();               
            //         }
            //     }else if($diferenca < 0){               
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
            //     }
            // }
                      
    }
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
