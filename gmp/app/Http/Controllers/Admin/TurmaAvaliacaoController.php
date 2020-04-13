<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\AvaliacaosExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Dompdf\Dompdf;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Professor;
use App\Turma;
use App\Aluno;
use App\Avaliacao;
use App\Epoca;
use App\Curso;
use App\Area;
use App\Instituicao;
use App\Healper\Nota;
use App\Relatorios\Minipauta;
use App\Events\AvaliacaoChanged;
use Illuminate\Support\Facades\View;



class TurmaAvaliacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($turma_id,$disciplina_id)
    {    
       set_time_limit(120);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $disciplina = Disciplina::find($disciplina_id);
       $modulo = $turma->modulo()->get();
       $curso = Curso::find($modulo->first()->curso_id);
       $epoca = Epoca::where('activo','S')->first();
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Disciplinas","url"=>route('turmas.disciplinas.index',[$turma->id])],       
        ["titulo"=>"Professores","url"=>""]
    ]); 
       $data = ['turma_id'=>$turma_id,'disciplina_id'=>$disciplina_id,'paginate'=>1000];            
       
       $listaModelo = Turma::listaAvaliacoes($data);       
               
       foreach ($listaModelo as $key => $avaliacao) {

         $aluno = Aluno::where('nome', $avaliacao->nome)->first();        
         $avaliacoes_do_aluno = Turma::avaliacoesDoAluno($aluno->id);

         $avaliacoes_do_aluno = $avaliacoes_do_aluno->where('disciplina_id',$disciplina->id)->sortBy('ano_lectivo');
         

         $ca_10 = $avaliacoes_do_aluno->where('modulo',$curso->acronimo . ' ' . '10ª')->last();
         $ca_11 = $avaliacoes_do_aluno->where('modulo',$curso->acronimo . ' ' . '11ª')->last();
         $ca_12 = $avaliacoes_do_aluno->where('modulo',$curso->acronimo . ' ' . '12ª')->last();


         if(!is_null($ca_10)){
         // dd($ca_10->exame2 != null ?  $ca_10->exame2 : ($ca_10->exame1 != null ?  $ca_10->exame1 : $ca_10->notafinal));
              $avaliacao->ca_10 = round(floatVal($ca_10->exame2 != null ?  $ca_10->exame2 : ($ca_10->exame1 != null ?  $ca_10->exame1 : $ca_10->notafinal)));
         }
         if(!is_null($ca_11)){
              $avaliacao->ca_11 = round(floatVal($ca_11->exame2 != null ?  $ca_11->exame2 : ($ca_11->exame1 != null ?  $ca_11->exame1 : $ca_11->notafinal)));          
         }
         if(!is_null($ca_12)){
              $avaliacao->ca_12 = round(floatVal($ca_12->exame2 != null ?  $ca_12->exame2 : ($ca_12->exame1 != null ?  $ca_12->exame1 : $ca_12->notafinal)));  
          }       
         
       }

       $listaCabecalho2 = collect([]);        
       $avaliacoes = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       $alunos_da_turma = $disciplina->buscarAlunosDaDisciplina($turma)->first();
       $listaAlunosNaoAvaliados = collect([]);      
       foreach ($alunos_da_turma as $aluno) {
               if($aluno->status == 'Activo'){
                   $aluno = Aluno::find($aluno->id);               
                   $aluno_avaliacoes = $aluno->avaliacaos()->where('disciplina_id',$disciplina->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id);                   
                  if($aluno_avaliacoes->isEmpty()){
                    $listaAlunosNaoAvaliados->push($aluno);
                  }
               }       
           
       }
    

       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();
       // $avaliacaos = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       
       // dd($listaModelo);
       // $listaModelo = $turma->listaAvaliacoes2($disciplina->id);

          

       return view('admin.turmas.disciplinas.avaliacaos.index',compact('turma','disciplina','listaMigalhas','listaModelo','listadisciplinas','listaProfessores','user','listaAlunosNaoAvaliados','epoca','listaCabecalho2'));

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
        if($request->has('professor_id')){            
            $data = $request->except('_token');
           
            // $avaliacao = Avaliacao::where('aluno_id',$data['aluno_id'])->get()
            //            ->where('turma_id',$data['turma_id'])
            //            ->where('disciplina_id',$data['disciplina_id']);
            // if($avaliacao->isNotEmpty()){              
            //     $erro = "Erro: Avaliacão já existente !!!";
            //         return redirect()->back()->withErrors($erro)->withInput();
            // }else{
                $user = auth()->user();
                $data['user_id'] = $user->id;
                $disciplina = Disciplina::find($data['disciplina_id']);
                $aluno = Aluno::find($data['aluno_id']);
                $turma = Turma::find($data['turma_id']);
                $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first();
                if(is_null($professor)){
                    $erro = "Erro: Nenhum professor associado a disciplina !!!";
                    return redirect()->back()->withErrors($erro)->withInput();
                }else if($user->admin == 'N' && $user->email != $professor->email){
                    $erro = "Erro: Nao tem permicao para fazer alteracoes !!!";
                    return redirect()->back()->withErrors($erro)->withInput();
                }else{             
                    $data['professor_id'] = $professor->id; 
                    // dd($data);                       
                    $avaliacao = Avaliacao::create($data);
                    $avaliacao->turma()->associate($turma);
                    $avaliacao->save();
                    event(new AvaliacaoChanged($professor));
                  return redirect()->back();    
                }
           

            }else if($request->has('excel_file')){
               $data = $request->except(['_token','excel_file']);
               foreach (request()->file('excel_file') as $file){

                $validacao = Excel::import(new AvaliacaosExcelImport,$file);

               }           
                              
                return redirect()->route('turmas.disciplinas.avaliacaos.index',$data)->withErrors($validacao)->withInput();
            }else{
                return redirect()->back();
            }
            
            
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
    public function show($id)    {
       // return Modulo::find($moduloId,$ProfessorId);

        return Avaliacao::find($id);
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
        $user = auth()->user();                       

        $avaliacao = Avaliacao::find($id);
        if($avaliacao->status != 'bloqueado' || $user->admin == 'S'){
            $epoca = Epoca::where('activo','S')->first();
            // if($epoca->fechado == 'S'){
            // $data = $request->only('_method','_token','exame1','exame2');         
            // }else{
            $data = $request->all(); 
            // }
              
            // $validacao = \Validator::make($data,[
            // "Professor" => "required",        
            // "carga" => "required",       
                    
            // ]);
            // if($validacao->fails()){
            //     return redirect()->back()->withErrors($validacao)->withInput();
            // }
               
                $disciplina = Disciplina::find($avaliacao->disciplina_id);
                $turma = Turma::find($avaliacao->turma_id);
                $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first();
                if(is_null($professor)){
                    $erro = "Erro: Nenhum professor associado a disciplina !!!";
                    return redirect()->back()->withErrors($erro)->withInput();
                }else if($user->admin == 'N' && $user->email != $professor->email){
                    $erro = "Erro: Nao tem permicao para fazer alteracoes !!!";
                    return redirect()->back()->withErrors($erro)->withInput();
                }else{
                    $avaliacao->user()->associate($user);
                    $avaliacao->professor()->associate($professor);
                    $avaliacao->turma()->associate($turma);
                    $avaliacao->save();
                    $avaliacao->update($data);

                }
                

                // $turma->attach($user);
                $professor = $avaliacao->professor;
                // ProcessPodcast::dispatch($podcast);
                
                event(new AvaliacaoChanged($professor));
                
            
       
            return redirect()->back();

        }else{
            $erro = "Nota congelada: consulte o administrador !!!";
            return redirect()->back()->withErrors($erro)->withInput();
        }
        
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
     $epoca = Epoca::where('activo','S')->first();
     if($epoca->fechado == 'N'){
       Avaliacao::find($id)->delete();      
     }
        return redirect()->back();
    }

    public function fileUpload($turma_id,$disciplina_id){ 
      $turma = Turma::find($turma_id);       
      $disciplina = Disciplina::find($disciplina_id);       
        return view('admin.turmas.disciplinas.avaliacaos.upload',compact('turma','disciplina'));
   }
    public function myPdfMethod($turma_id,$disciplina_id)
    {
        set_time_limit(120);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $disciplina = Disciplina::find($disciplina_id);
       $modulo = $turma->modulo()->get();
       $curso = Curso::find($modulo->first()->curso_id);
       $epoca = Epoca::where('activo','S')->first();
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Disciplinas","url"=>route('turmas.disciplinas.index',[$turma->id])],       
        ["titulo"=>"Professores","url"=>""]
    ]);             
       $data = ['turma_id'=>$turma_id,'disciplina_id'=>$disciplina_id,'paginate'=>100];
       $listaModelo = Turma::listaAvaliacoes($data);
       // dd($listaModelo);

       foreach ($listaModelo as $key => $avaliacao) {            
         
         $aluno = Aluno::where('nome', $avaliacao->nome)->first();        
         $avaliacoes_do_aluno = Turma::avaliacoesDoAluno($aluno->id);

         $avaliacoes_do_aluno = $avaliacoes_do_aluno->where('disciplina_id',$disciplina->id)->sortBy('ano_lectivo');
         

         $ca_10 = $avaliacoes_do_aluno->where('modulo',$curso->acronimo . ' ' . '10ª')->last();
         $ca_11 = $avaliacoes_do_aluno->where('modulo',$curso->acronimo . ' ' . '11ª')->last();
         $ca_12 = $avaliacoes_do_aluno->where('modulo',$curso->acronimo . ' ' . '12ª')->last();


         if(!is_null($ca_10)){
         // dd($ca_10->exame2 != null ?  $ca_10->exame2 : ($ca_10->exame1 != null ?  $ca_10->exame1 : $ca_10->notafinal));
              $avaliacao->ca_10 = floatVal($ca_10->exame2 != null ?  $ca_10->exame2 : ($ca_10->exame1 != null ?  $ca_10->exame1 : $ca_10->notafinal));
         }
         if(!is_null($ca_11)){
              $avaliacao->ca_11 = floatVal($ca_11->exame2 != null ?  $ca_11->exame2 : ($ca_11->exame1 != null ?  $ca_11->exame1 : $ca_11->notafinal));          
         }
         if(!is_null($ca_12)){
              $avaliacao->ca_12 = floatVal($ca_12->exame2 != null ?  $ca_12->exame2 : ($ca_12->exame1 != null ?  $ca_12->exame1 : $ca_12->notafinal));  
          }       
         
       }

       $listaCabecalho2 = collect([]);        
       $avaliacoes = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       $alunos_da_turma = $disciplina->buscarAlunosDaDisciplina($turma)->first();
       $listaAlunosNaoAvaliados = collect([]);      
       foreach ($alunos_da_turma as $aluno) {
               if($aluno->status == 'Activo'){
                   $aluno = Aluno::find($aluno->id);               
                   $aluno_avaliacoes = $aluno->avaliacaos()->where('disciplina_id',$disciplina->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id);                   
                  if($aluno_avaliacoes->isEmpty()){
                    $listaAlunosNaoAvaliados->push($aluno);
                  }
               }       
           
       }
    

       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();
       // $avaliacaos = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       
       // dd($listaModelo);
       // $listaModelo = $turma->listaAvaliacoes2($disciplina->id);       
        $viewhtml = View::make('admin.relatorios.minipautas.index',compact('turma','disciplina','listaMigalhas','listaModelo','listadisciplinas','listaProfessores','user','listaAlunosNaoAvaliados','epoca','listaCabecalho2'))->render();
          
          $pdf = new Dompdf;
          $pdf->loadHTML($viewhtml);      
          $pdf->set_paper('A4','landscape');
          $pdf->render();      
          return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
          exit(0);
    }

   

}
