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
use App\Events\AvaliacaoChanged;



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
       // dd($epoca); 
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Disciplinas","url"=>route('turmas.disciplinas.index',[$turma->id])],       
        ["titulo"=>"Professores","url"=>""]
    ]);             
       
       $listaModelo = Turma::listaAvaliacoes($turma_id,$disciplina_id,100);

       foreach ($listaModelo as $key => $avaliacao) {
        // dd($avaliacao);
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

   function pdf($turma_id,$disciplina_id){
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_to_html($turma_id,$disciplina_id));      
      $pdf->set_paper('A4','landscape');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }
  function convert_to_html($turma_id,$disciplina_id){

       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $ano_lectivo = $turma->ano_lectivo;
       $disciplina = Disciplina::find($disciplina_id);
       $modulo = $turma->modulo()->first();
       $curso = Curso::find($modulo->curso_id);       
       $area = Area::find($curso->area_id);       
       $classe = Classe::find($modulo->classe_id);
       $epoca = Epoca::where('activo','S')->first();          
       $listaModelo = Turma::listaAvaliacoes($turma_id,$disciplina_id,100);
       $total_alunos = $listaModelo->count(); 
       $instituicao = Instituicao::all()->first();      

       if($epoca->trimestre == 'I'){               
         $aptos = round(($listaModelo->where('ct1','>=',10)->count() * 100)/$total_alunos,1);  
         $naptos = round(($listaModelo->where('ct1','<',10)->count() * 100)/$total_alunos,1);       
         $desistencia = round(($listaModelo->where('mac1','=',null)->count() * 100)/$total_alunos,1);       
         $notas = $listaModelo->where('ct1','!=',null);
         $notas = $notas->sortBy('ct1');
         $min = $notas->first()->ct1; 
         $max = $notas->last()->ct1;
         $valores = collect([]);
         foreach ($listaModelo as $key => $value) {
           $valores->push($value->ct1);
         }
         $media = $valores->median();

       }else if($epoca->trimestre == 'II'){ 
         $aptos = round(($listaModelo->where('ct2','>=',10)->count() * 100)/$total_alunos,1);  
         $naptos = round(($listaModelo->where('ct2','<',10)->count() * 100)/$total_alunos,1);         
         $desistencia = round(($listaModelo->where('mac2','=',null)->count() * 100)/$total_alunos,1);       
         $notas = $listaModelo->where('ct2','!=',null);
         $notas = $notas->sortBy('ct2');      
         $min = $notas->first()->ct2; 
         $max = $notas->last()->ct2;
         $valores = collect([]);
         foreach ($listaModelo as $key => $value) {
           $valores->push($value->ct2);
         }
         $media = $valores->median();


       }else{
         $aptos = round(($listaModelo->where('notafinal','>=',10)->count() * 100)/$total_alunos,1);  
         $naptos = round(($listaModelo->where('notafinal','<',10)->count() * 100)/$total_alunos,1);
         $desistencia = round(($listaModelo->where('notafinal','=',null)->count() * 100)/$total_alunos,1);       
         $notas = $listaModelo->where('mac3','!=',null);
         $notas = $notas->sortBy('notafinal');      
         $min = $notas->first()->notafinal; 
         $max = $notas->last()->notafinal;
         $valores = collect([]);
         foreach ($listaModelo as $key => $value) {
           $valores->push($value->notafinal);
         }
         $media = $valores->median();

       } 


       $avaliacoes = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       $alunos_da_turma = $turma->alunos()->get();
       $turma_info = explode(' ', $turma->nome);
       $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first(); 

  $output =" 
      <!DOCTYPE html>
      <html>
      <head>
      <title>PAUTA do $epoca->trimestre trimestre da turma $turma->nome</title>      
      <style>
         .vermelhado td{
          color:red;
          border: solid 1px black;
         }         
         .centro {
          text-align:center;
         }
         .vtexto{
            -ms-transform: rotate(270deg); /* IE 9 */
            -webkit-transform: rotate(270deg); /* Safari 3-8 */
             transform: rotate(270deg);
             font-weight:bold;
             font-size:8px;             
             text-align:center;
             margin: 15px -10px;
             
         }
        #cabecalho,#seccao_topo_grupo,#rodape{
          text-transform: uppercase;
          font-size:11px;
        }
        #cabecalho p>span{
          color:red;
        }
        #cabecalho{
          margin-top:-50px;         
        }
        #cabecalho p{
          margin-bottom:-12px;
       }
       #seccao_topo_grupo{
          position:relative;
          top:10px;
       }
       #seccao_topo_grupo  #seccao_1{
          float:left;
          width:17%;          
       }
       #seccao_topo_grupo  #seccao_2{
          float:left;
          width:15%;          
       }
       #seccao_topo_grupo  #seccao_3{
          float:left;
          width:35%;          
       }
       #seccao_topo_grupo > div{
          float:left;
          width:24%;          
       }
       #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-12px;
        font-size: 10px;
      }
      #seccao_3>p{
        margin-bottom: -6px;
      }
      #seccao_3>p>span{
        margin-right: 5px;
      }
       #rodape{;
          position:relative;          
          top:9%;
       }
       #rodape p{
         margin-top:-11px;
       }
       #rodape > div{
          float:left;
          width:50%;
       }       
       #mytable{
       width:100%;
       border-collapse:collapse;
       font-size:11px;

      }
       #tabela_area{
        background:transparent;
        position:relative;
        top:50px;
      }
      #mytable>th{
        text-align:center;
      }
      th{
        text-align:center;
      }
      th,td{
        border:1px solid;
        padding: -1px;
        padding-left: 1px;
        margin:-300px;        
      }
      </style>
      </head>
      <body onload='atribueCor()'>
  <div style='font-size: 10.5px;'>
   <div id='cabecalho' align='center' style='font-size: 9.5px;font-weight: bold;' class='table-responsive text-uppercase'>
      <p>$instituicao->nome</p>                           
      <p>$instituicao->lema</p>                                       
      <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>                  
      <p style='color:red;'>MINI-PAUTA</p> 
    </div>
    <div id='seccao_topo_grupo'>    
    <div id='seccao_1' class='col-md-3'>
        <p>ÁREA DE FORMAÇÃO:$area->nome</p>
        <p>Curso: $curso->nome</p>      
       <p>$classe->nome CLASSE TURMA: $turma_info[2] </p>         
    </div>
    <div id='seccao_2' align='center' class='col-md-6'>
       <p style='font-size:10px;margin-top:13.5px;'>ANO LECTIVO $turma->ano_lectivo</p> 
    </div>
    <div id='seccao_3' align='center' class='col-md-6'>
       <p><span style='font-weight:bold;font-size:10px;'>DISCIPLINA:</span> <span style='color:red;font-size:10px;' >$disciplina->acronimo</span> <span style='font-size:8px;'>($disciplina->nome)</span></p>     
    </div>
    <div id='seccao_3' align='right' class='col-md-6' style='margin-top:3.5px;margin-left:-20px;'>
       <p style='font-size: 9px;'>
        <span style='font-weight: bold;'>Aptos: $aptos%</span>
        <span style='font-weight: bold;'>N/Aptos: ".floatVal($naptos-$desistencia). "%</span>
         <span style='font-weight: bold;'>DESISTÊNCIAS: $desistencia%</span>
      </p> 
      <p style='font-size: 9px;'>    
        <span style='font-weight: bold;'>MÉDIA: $media</span>
        <span style='font-weight: bold;'>Max: $max</span>
        <span style='font-weight: bold;'>Min: $min</span>
      </p>
    </div>     
    </div>
    </div>
     <div id='tabela_area'>
      <table id='mytable' class='table table-bordered table-xs table-condensed' style='font-size:8px;'>
      <thead>
      <tr style='font-weight: bold;'>
      <th scope='col' rowspan='3'>Nº</th>    
      <th scope='col' rowspan='3' style='text-align:left;font-size:8;'><span style='margin-left:2px;''>NOME COMPLETO</span></th>  
      <th scope='col' rowspan='3' style='text-orientation:vertical);'><p class='vtexto' style='margin:17px -5px;font-size:8px;'>IDADE</p></p></th>
      <th scope='col' rowspan='3'><p class='vtexto' style='margin:17px -8px;font-size:8px;'>FALTAS</p></p></th>";
      if($ano_lectivo < 2019){
          $output .="      
          <th scope='col' colspan='4'>I TRIMESTRE</th>";
      }else{
          $output .="      
          <th scope='col' colspan='3'>I TRIMESTRE</th>";
        }
         $output .="
        <th scope='col' rowspan='3'><p class='vtexto' style='margin:17px -8px;font-size:8px;'>FALTAS</p></p></th>";

      if($ano_lectivo < 2019){
        $output .="      
         <th scope='col' colspan='6'>II TRIMESTRE</th>";
      }else{
        $output .="      
        <th scope='col' colspan='5'>II TRIMESTRE</th>";
      }
       $output .="
      <th scope='col' rowspan='3'><p class='vtexto' style='margin:17px -8px;font-size:8px;'>FALTAS</p></p></th>  
      <th scope='col' colspan='5'>III TRIMESTRE</th>     
      <th scope='col' colspan='5'>CLASSIFICAÇÃO ANUAL</th> 
    </tr>
    <tr>      
      <th scope='col' rowspan='2'>MAC</th>  
      <th scope='col' rowspan='2'>P1</th>";
      if($ano_lectivo < 2019){
        $output .="      
        <th scope='col' rowspan='2'>P2</th>";
      }
        $output .="      
      <th scope='col' rowspan='2'>CT1</th>";
      if($ano_lectivo < 2019){
        $output .="      
        <th scope='col' rowspan='2'>P2</th>";
      }
        $output .="            
       <th scope='col' colspan='2'>AVALIAÇÕES</th>  
      <th scope='col' rowspan='2'>CF2</th>      
      <th scope='col' rowspan='2'>CT1</th>
      <th scope='col' rowspan='2'>CT2</th>
      <th scope='col' colspan='2'>AVALIAÇÕES</th>  
      <th scope='col' rowspan='2'>CF3</th>      
      <th scope='col' rowspan='2'>CT2</th>      
      <th scope='col' rowspan='2'>CT3</th>  
      <th scope='col' rowspan='2'>MTC</th>      
      <th scope='col' rowspan='2'>60%</th>      
      <th scope='col' rowspan='2'>PG</th>      
      <th scope='col' rowspan='2'>40%</th>      
      <th scope='col' rowspan='2'>60% + 40%</th>
      </tr>
      <tr>     
        <th scope='col'>MAC</th>  
        <th scope='col'>P1</th>
        <th scope='col' >MAC</th>  
        <th scope='col' >P1</th>      
      </tr>    
  </thead>
  <tbody>";
        $counter = 0;    
        $counter2 = 0;    
        $fundo = '';
        $font_size = 0;
        
        foreach($listaModelo as $aluno){
          if($aluno->status == 'Desistido'){
            $fundo ='yellow';
          }else{
            $fundo = '';

          }
          if($listaModelo->count() > 45){
              $font_size = 9.8;              

          }else{
              $font_size = 10;
          }
          // $counter2++;
          // if($counter2%2!=0){
          //   $fundo = '#fafafa';
          // }else{
          //   $fundo = '#fff';
          // }
        $output .="
        <tr style='font-size: $font_size px;background:$fundo;'>";
        foreach ($aluno as $key => $value){         

         if($value != null && $value !=''){
           $cor = $value < 10 ? 'red':'black';
         if($key == 'id' || 
            $key == 'numero' || 
            $key == 'nome' || 
            $key == 'idade' ||
            $key == 'fnj1' ||
            $key == 'fnj2' ||
            $key == 'fnj3' ||
            $key == 'sessenta' ||
            $key == 'quarenta'
           ){
           $cor = 'black';
         // if($counter < 4 || $counter == 5 || $counter == 9 || $counter == 16 || $counter == 23 || $counter == 25){
         //   $cor = 'black';
         }
         } 
        if($counter!=0 && $key != 'status' &&  $key !='ca10' &&  $key !='ca11' &&  $key !='ca12'){          
          if($counter == 2){
          $output .="<td style='background:$fundo'><span style='color:$cor;'>$value</span></td>";
          }else{
          $output .="<td style='background:$fundo' class='centro'><span style='color:$cor'>$value</span></td>";

          }
        }       
        $counter++;             
                 }
        $counter = 0;         
        $output .="
        </tr>";
    }
    $output .=" 
  </tbody>
</table>
</div>

<div id='rodape' align='center'>
      <div class='col-md-6 text-uppercase'>
       <p>O(A) PROFESSOR(A)                 
       <p>_________________________________</p> 
       <p>$professor->nome</p> 
      </div>      
      <div class='col-md-6'>
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>$instituicao->director_pedagogico</p>
      </div>    
    </body>
</html";

      return $output;
       

  } 


}
