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
                         'professor_id'=>$professor->id,
                         'director'=>$data['director']
                         
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
        top:80px;
      }
      #tabela_area2{
        background:transparent;
        position:relative;
        top:14%;
      }
      #professor_area{
        background:transparent;
        position:relative;
        font-size:12px;
        font-weight:bold;
        top:14%;
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

      <div id='cabecalho' align='center' style='font-size: 12px;font-weight: bold;'' class='table-responsive text-uppercase'>
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
                    <p style='text-transform:uppercase;'>$turma->nome</p>
                </th>";
            }
    $output .="
        <th style='width:0.1%' scope='col' rowspan='2' class='centro'>TOTAL</th>      
    </tr>
    <tr>";       
    foreach($turmas as $key => $turma){
        $disciplina_id = $turma->pivot->disciplina_id;       
        $disciplina = Disciplina::find($disciplina_id);
    $output .="
    <th style='font-size:8px;' scope='col' class='centro'>
        <p>$disciplina->acronimo</p>
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
                 <th scope='col'><p style='font-size:12px;margin-left:5px;'>$value</p></th>";
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
                    <tr>
                        <th><span style='font-size:12px;margin-left:5px;'>CURSO</span></th></th>
                        <th><span style='font-size:12px;margin-left:5px;'>ACRÓNIMO</span></th></th>
                        <th><span style='font-size:12px;margin-left:5px;'>COORDENADOR</span></th></th>                        
                    </tr>
                </thead>
                <tbody>";
                foreach ($dados['data2'] as $dado2) {
                   $output.="<tr style='text-transform:uppercase;'>"; 
                   foreach ($dado2 as $value) {
                       $output .="                 
                        <td><span style='font-size:12px;margin-left:5px;'>$value</span></th></td>";
                   }
                        $output.="</tr>";
                }
             $output.="   
                </tbody>
            </table>
        </div>

        
      <div id='professor_area'>
       <p>NOME DO PROFESSOR: $professor->nome</p>     
       <p>COORDENADOR DO TURNO:</p>     
    </div>    
    </body>
   </html";

      return $output;
    }

}
