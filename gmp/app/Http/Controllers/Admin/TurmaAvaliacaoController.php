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


class TurmaAvaliacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($turma_id,$disciplina_id)
    {    
         // dd($disciplina_id);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $disciplina = Disciplina::find($disciplina_id);
       $modulo = $turma->modulo()->get();
       $epoca = Epoca::where('activo','S')->first();
       // dd($epoca);
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Disciplinas","url"=>route('turmas.disciplinas.index',[$turma->id])],       
        ["titulo"=>"Professores","url"=>""]
    ]);
       
       $listaModelo = Turma::listaAvaliacoes($turma_id,$disciplina_id,100);
       $listaCabecalho2 = $turma->disciplinas()->get();           
       $avaliacoes = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       $alunos_da_turma = $turma->alunos()->get();
       $listaAlunosNaoAvaliados = collect([]);      
       foreach ($alunos_da_turma as $aluno) {
            // $aluno = Aluno::find(132);
           
              $aluno_avaliacoes = $aluno->avaliacaos()->where('disciplina_id',$disciplina->id)->get()->where('aluno_id',$aluno->id)->where('turma_id',$turma->id);
              if($aluno_avaliacoes->isEmpty()){
                $listaAlunosNaoAvaliados->push($aluno);
              }else{
                
              }
           
       }
    

       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();
       $avaliacaos = $turma->avaliacaos()->where('disciplina_id',$disciplina->id)->get();
       
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
            $user = auth()->user();
            $data['user_id'] = $user->id;
            $disciplina = Disciplina::find($data['disciplina_id']);
            $aluno = Aluno::find($data['aluno_id']);
            $turma = Turma::find($data['professor_id']);
            $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first();
                         
            $data['professor_id'] = $professor->id;                        
            $avaliacao = Avaliacao::create($data);
            $avaliacao->turma()->associate($turma);
            $avaliacao->save();
            
            return redirect()->back();    

            }else if($request->has('excel_file')){
              
               foreach (request()->file('excel_file') as $file){

                Excel::import(new AvaliacaosExcelImport,$file);

               }           
                return redirect()->back();
                // return redirect()->route('turmas.disciplinas.avaliacaos',[]);
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
        $avaliacao = Avaliacao::find($id);
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
        
        $user = auth()->user();                       
            $disciplina = Disciplina::find($avaliacao->disciplina_id);
            $turma = Turma::find($avaliacao->turma_id);
            $professor = $turma->professores()->where('disciplina_id',$disciplina->id)->first();
            $avaliacao->user()->associate($user);
            $avaliacao->professor()->associate($professor);
            $avaliacao->turma()->associate($turma);
            $avaliacao->save();
            $avaliacao->update($data);
            

            // $turma->attach($user);
        
   
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
     $epoca = Epoca::where('activo','S')->first();
     if($epoca->fechado == 'N'){
       Avaliacao::find($id)->delete();      
     }
        return redirect()->back();
    }

    public function fileUpload(){
        return view('admin.turmas.disciplinas.avaliacaos.upload');
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
       $aptos = round(($listaModelo->where('notafinal','>=',10)->count() * 100)/$total_alunos,1);  
       $naptos = round(($listaModelo->where('notafinal','<',10)->count() * 100)/$total_alunos,1);       
       $notas = $listaModelo->where('notafinal','!=',null);
       $notas = $notas->sortBy('notafinal');      
       $min = $notas->first()->notafinal; 
       $max = $notas->last()->notafinal; 

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
            -ms-transform: rotate(60deg); /* IE 9 */
            -webkit-transform: rotate(90deg); /* Safari 3-8 */
             transform: rotate(90deg);
             font-weight:bold;
             font-size:11px;            
             
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
          width:13%;          
       }
       #seccao_topo_grupo  #seccao_3{
          float:left;
          width:34%;          
       }
       #seccao_topo_grupo > div{
          float:left;
          width:24%;          
       }
       #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-12px;
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
  <div tamanho='12'>
   <div id='cabecalho' align='center' style='font-size: 11px;font-weight: bold;' class='table-responsive text-uppercase'>
      <p>COLÉGIO PADRE BUILU</p>                           
      <p>FÉ E CIÊNCIA</p>                                       
      <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
      <p>PAUTA DE APROVEITAMENTO</p>            
      <p>ÁREA DE FORMAÇÃO:$area->nome</p>           
      <p style='color:red;'>REGIME DIURNO ( $turma->periodo )</p> 
    </div>
    <div id='seccao_topo_grupo'>    
    <div id='seccao_1' class='col-md-3'>
       <p>ANO LECTIVO $turma->ano_lectivo</p> 
       <p>$classe->nome CLASSE TURMA: $turma_info[2] </p>         
    </div>
    <div id='seccao_2' align='center' class='col-md-6'>
       <p>Curso: $curso->nome</p>      
    </div>
    <div id='seccao_3' align='center' class='col-md-6'>
       <p><span style='font-weight: bold;'>DISCIPLINA:</span> <span style='color:red;'>$disciplina->acronimo</span> ($disciplina->nome)</p>     
    </div>
    <div id='seccao_3' align='center' class='col-md-6'>
       <p style='font-size: 9px;;'>
        <span style='font-weight: bold;'>Aptos:</span> <span style='font-weight:bold;color:red;'>$aptos%  
        </span>
        <span style='font-weight: bold;'>N/Aptos:</span> <span style='font-weight:bold;color:red;'>$naptos%
        </span>
        <span style='font-weight: bold;'>Max:</span> <span style='color:red;'>$max
        </span>
        <span style='font-weight: bold;'>Min:</span> <span style='color:red;'>$min
        </span>
      </p>     
    </div>     
    </div>
    </div>
     <div id='tabela_area'>
      <table id='mytable' class='table table-bordered table-xs table-condensed' style='font-size:10px;'>
      <thead>
      <tr style='font-weight: bold;'>
      <th scope='col' rowspan='2'>#</th>    
      <th scope='col' rowspan='2' style='text-align:left;font-size:11;'>Nome completo</th>  
      <th scope='col' rowspan='2' style='text-orientation:vertical);'>Id</th>
      <th scope='col' rowspan='2'>F</th>";
      if($ano_lectivo < 2019){
          $output .="      
          <th scope='col' colspan='4'>I TRIMESTRE</th>";
      }else{
          $output .="      
          <th scope='col' colspan='3'>I TRIMESTRE</th>";
        }
         $output .="
        <th scope='col' rowspan='2'>F</th>";

      if($ano_lectivo < 2019){
        $output .="      
         <th scope='col' colspan='6'>II TRIMESTRE</th>";
      }else{
        $output .="      
        <th scope='col' colspan='5'>II TRIMESTRE</th>";
      }
       $output .="
      <th scope='col' rowspan='2'>F</th>  
      <th scope='col' colspan='5'>III TRIMESTRE</th>     
      <th scope='col' colspan='5'>Classificação Anual</th> 
    </tr>
    <tr>      
      <th scope='col'>Mac</th>  
      <th scope='col'>P1</th>";
      if($ano_lectivo < 2019){
        $output .="      
        <th scope='col'>P2</th>";
      }
        $output .="      
      <th scope='col'>CT1</th>  
      <th scope='col'>Mac</th>  
      <th scope='col'>P1</th>";
      if($ano_lectivo < 2019){
        $output .="      
        <th scope='col'>P2</th>";
      }
        $output .="            
      <th scope='col'>CF2</th>      
      <th scope='col'>CT1</th>
      <th scope='col'>CT2</th>      
      <th scope='col'>Mac</th>  
      <th scope='col'>P1</th>      
      <th scope='col'>CF3</th>      
      <th scope='col'>CT2</th>      
      <th scope='col'>CT3</th>  
      <th scope='col'>MTC</th>      
      <th scope='col'>60%</th>      
      <th scope='col'>PG</th>      
      <th scope='col'>40%</th>      
      <th scope='col'>60% + 40%</th>
    </tr>    
  </thead>
  <tbody>";
        $counter = 0;    
        $counter2 = 0;    
        $fundo = '';    
        foreach($listaModelo as $aluno){
                
          if($aluno->status == 'Desistido'){
            $fundo ='yellow';
          }else{
            $fundo = '';

          }
          // $counter2++;
          // if($counter2%2!=0){
          //   $fundo = '#fafafa';
          // }else{
          //   $fundo = '#fff';
          // }
        $output .="
        <tr style='font-size: 12px;background:$fundo;'>";
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
        if($counter!=0 && $key != 'status'){          
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
       <p>O(A) PROFESSOR(A) DE TURMA                  
       <p>_________________________________</p> 
       <p>$professor->nome</p> 
      </div>      
      <div class='col-md-6'>
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>ERNESTO TIGRE ISSAMBO</p>
      </div>    
    </body>
</html";

      return $output;
       

  } 


}
