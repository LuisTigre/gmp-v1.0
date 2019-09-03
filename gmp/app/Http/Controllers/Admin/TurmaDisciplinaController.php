<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Professor;
use App\Turma;
use App\Instituicao;
use App\Epoca;
use PDF;
use App\Exports\PautaExport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;


class TurmaDisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($turma_id)
    {  

       $user = auth()->user();       
       $turma = Turma::find($turma_id);      
       $modulo = $turma->modulo()->get();
       $epoca = Epoca::where('Activo','S')->first();       
       // $disciplinas = $modulo->withPivot()->get();       
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Disciplinas e Professores","url"=>""]
    ]);
       
       $listaModelo = Turma::listaDisciplinas($turma_id,14);          
       $listaDisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();

       

       return view('admin.turmas.disciplinas.index',compact('turma','listaMigalhas','listaModelo','listaProfessores','listaDisciplinas','user','epoca'));

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

            $disciplina = Turma::find($data['turma_id'])->disciplinas()->get()->where('id',$data['disciplina_id'])->first();
            if(is_null($disciplina)){
              return redirect()->back()->withErrors("Disciplina não faz parte da turma")->withInput();  
            }else if(is_null($disciplina->pivot->professor_id)){           
              // Turma::find($data['turma_id'])->disciplinas()->attach($data['professor_id'],$data);
              Turma::find($data['turma_id'])->disciplinas()->updateExistingPivot($data['disciplina_id'],$data);
              return redirect()->back();              
            }else{
              $professor = Professor::find($disciplina->pivot->professor_id);
              return redirect()->back()->withErrors("O professor " . $professor->nome . " já faz parte da referida turma")->withInput();             
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
        if(is_null($disciplina)){
          $disciplina = Disciplina::where('nome',$data['disciplina'])->first();
          

        }       
        $data['user_id'] = $user->id; 
            $turma = Turma::find($data['turma_id']);            
            $professor = Professor::where('nome',$data['professor'])->first();
            if(is_null($professor)){
              $professor = Professor::find($data['professor_id']);
            }
            // dd($professor);
            $updated_data = [
                         'disciplina_id'=> $disciplina->id,
                         'disciplina_turma.user_id'=>$user->id,
                         'professor_id'=>$professor->id,
                         'director'=>$data['director']
                         
                        ];

            if($data['disciplina_id'] != ''){

              $turma->disciplinas()->updateExistingPivot($data['disciplina_id'],$updated_data);

            }else{             
              $disciplina = Turma::find($data['turma_id'])->disciplinas()->get()->where('id',$disciplina->id)->first();              
              if(!is_null($disciplina) && is_null($disciplina->pivot->professor_id) || $disciplina->pivot->professor_id == $professor->id){
                $professor->disciplinas()->updateExistingPivot($data['turma_id'],$updated_data);
              }else{
                 $professor = Professor::find($disciplina->pivot->professor_id);
              return redirect()->back()->withErrors("O professor " . $professor->nome . " já faz parte da referida turma")->withInput();
              }
             

            } 
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
    public function destroy($turma_id,$disciplina_id)
    {        
       
       // $professor =Turma::find($turma_id)->professores()->where('disciplina_id',$disciplina_id)->get();
       // dd($professor);
      $user = auth()->user();
       
       $updated_data = [
                         'turma_id'=> $turma_id,                         
                         'disciplina_id'=> $disciplina_id,                         
                         'professor_id'=>null,                      
                         'user_id'=>$user->id,                      
                        ];
           
          Turma::find($turma_id)->disciplinas()->updateExistingPivot($disciplina_id,$updated_data);
       //  // Professor::find($professorId)->turmas()->updateExistingPivot($turmaId,$updated_data);
            
        return redirect()->back();
    }

    public function estatistica($epoca_id,$turma_id,$disciplina_id)
    {
      set_time_limit(1000);
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->estatistica_html($epoca_id,$turma_id,$disciplina_id));      
      $pdf->set_paper('A4','portrait');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
    }
    function estatistica_html($epoca_id,$turma_id,$disciplina_id){
      $user = auth()->user();
       $turma = Turma::find($turma_id);  
       $ano_lectivo = $turma->ano_lectivo;     
       $professor = $turma->professores()->where('disciplina_id',$disciplina_id)->first();
      
       $dados = $professor->estatistica($epoca_id); 
       // dd($dados);             
       $turmas = $professor->turmas()->where('ano_lectivo',$ano_lectivo)->get();
       $instituicao = Instituicao::all()->first(); 

      $output =" 
      <!DOCTYPE html>
      <html>
      <head>
      <title>$professor->nome</title>      
      <style>
         
         .centro {
          text-align:center;
         }
         .vtexto{
            -ms-transform: rotate(270deg); /* IE 9 */
            -webkit-transform: rotate(270deg); /* Safari 3-8 */
             transform: rotate(270deg);
             font-weight:bold;
             font-size:12px;             
             text-align:center;
             margin: 15px -10px;
             
         }
         .otexto{
            -ms-transform: rotate(50deg); /* IE 9 */
            -webkit-transform: rotate(50deg); /* Safari 3-8 */
             transform: rotate(50deg);
             font-weight:bold;
             font-size:11px;            
             height:20px;
             margin-top:-8px;
             margin-bottom:-40px;
             margin-left:-10px;
             margin-right:-4px;
             word-break: keep-all;
             
         }   

        #cabecalho,#seccao_topo_grupo,#rodape{ 
          text-transform: uppercase;
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
       
       table{
       width:100%;
       border-collapse:collapse;
       font-size:11px;

      }
       #tabela_area{
        background:transparent;
        position:relative;
        top:20px;
      }
      #tabela_area2{
        background:transparent;
        position:relative;
        top:4.5%;
      }
      #professor_area{
        background:transparent;
        position:relative;
        font-size:10px;
        font-weight:bold;
        top:4%;
      }
      #mytable>th{
        text-align:center;
      }
      th,td{
        border:1px solid;
        padding: 1px;
        margin:-200px;
      }
      </style>
      </head>
      <body onload='atribueCor()'>

      <div id='cabecalho' align='center' style='font-size:9px;font-weight: bold; class='table-responsive text-uppercase'>
        <p>$instituicao->nome</p>                           
        <p>$instituicao->lema</p>                                       
        <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
        <p>ESTATÍSTICA POR CLASSE E DISCIPLINA</p>
      </div>
      
    <div id='tabela_area'>
    <table id='mytable'>
    <thead border:1px solid;>
    <tr style='font-weight: bold;'>
        <th class='centro' scope='col' rowspan='2' ></th>";
            foreach($turmas as $key => $turma){
                $output .="
                <th scope='col' class='centro'>
                    <p class='otexto' style='margin:15px -20px;font-size:9px;text-transform:uppercase;'>$turma->nome</p>
                </th>";
            }
    $output .="
        <th style='width:0.2%' scope='col' rowspan='2' class='centro'><p class='otexto' style='font-size: 9px;margin:0px -10px;text-transform:uppercase;'>TOTAL</p></th>      
    </tr>
    <tr>";       
    foreach($turmas as $key => $turma){
        $disciplina_id = $turma->pivot->disciplina_id;       
        $disciplina = Disciplina::find($disciplina_id);
    $output .="
    <th style='font-size:8px;' scope='col' class='centro'>
        <p style='text-transform:uppercase;'>$disciplina->acronimo</p>
    </th>";
      }
       
      $output
       .="</tr>";

      foreach($dados['data'] as $key=>$dado){ 
        $output
       .="<tr>";
       $i =0;
        foreach($dado as $key=>$value){
            if($i == 0){
                $output .="
                 <th scope='col'><p style='font-size: 9px;margin-left:5px;'>$value</p></th>";
            }else{
                $output .="
                <th scope='col' class='centro'>$value</th>"; 
            }
          $i++;                 
        } 
        $output .="</tr>";           
      }
    


    
  $output .="</tbody>
</table>
</div>
<div>
        <div id='tabela_area2'>    
            <table>
                <thead>
                    <tr style='padding:2px;font-weight:bold;'>
                        <th><span style='font-size: 9px;margin-left:5px;'>CURSO</span></th></th>
                        <th><span style='font-size: 9px;margin-left:5px;'>ACRÓNIMO</span></th></th>
                        <th>
                          <span style='font-size: 9px;margin-left:5px;'>COORDENADOR</span>
                        </th>                        
                    </tr>
                </thead>
                <tbody>";
                foreach ($dados['data2'] as $dado2) {
                   $output.="<tr style='text-transform:uppercase;'>"; 
                   foreach ($dado2 as $value) {
                       $output .="                 
                        <td><span style='font-size: 9px;margin-left:5px;'>$value</span></th></td>";
                   }
                        $output.="</tr>";
                }
             $output.="   
                </tbody>
            </table>
        </div>

        
      <div id='professor_area'>
       <p style='text-transform:uppercase;'>NOME DO PROFESSOR: $professor->nome</p>     
       <p>COORDENADOR DO TURNO:</p>     
    </div>    
    </body>
   </html";
   

      return $output;
    }

}
