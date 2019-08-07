<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Curso;
use App\Classe;
use App\Disciplina;
use App\Turma;
use App\Aluno;
use App\Epoca;
use App\Sala;
use App\Aula;
use PDF;
use Dompdf\Dompdf;


class TurmasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $user = auth()->user();
       $epoca = Epoca::where('activo','S')->first(); 
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>""]
    ]);
       $quantidade_de_turmas = Turma::where('ano_lectivo',$epoca->ano_lectivo)->count();
       $quantidade_de_turmas = $quantidade_de_turmas <= 5 ? 24 : $quantidade_de_turmas;
       $listaModelo = Turma::listaModelo($quantidade_de_turmas);           
       $listaDisciplinas = Modulo::listaDisciplinas(1,5);
       // dd($listaDisciplinas);    
       $listaModulos = Modulo::all()->sortBy(['nome']);
       $listaCursos = Curso::all();
       $listaClasses = Classe::all();
       $ano_lectivo = $epoca->ano_lectivo;
       $salas = Sala::all()->sortBy('nome'); 


       // $modulo = Modulo::find(1);
       // foreach ($modulo->disciplinas as $disciplina) {
       //     dd($disciplina->pivot);
       // }

       

       return view('admin.turmas.index',compact('listaMigalhas','listaModelo','listaCursos','listaClasses','user','listaDisciplinas','listaModulos','ano_lectivo','salas'));

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
        $data = $request->except('_token');
        
        TurmasController::criar_nova_turma($data);
        return redirect()->back();
    }


    public static function criar_nova_turma($data){

        if(!isset($data['aluno_id'])){            
            $epoca = Epoca::where('activo','S')->first();            
            $alfabet = range('A', 'Z');
            $qtd = Turma::where('modulo_id','=',$data['modulo'])->where('ano_lectivo','=',$epoca->ano_lectivo)->count();            
            $letter = $alfabet[$qtd];            
            $modulo = Modulo::find($data['modulo']);            
            $data['nome'] = $modulo->nome . ' ' . $letter;

            $validacao = \Validator::make($data,[                  
            "modulo" => "required",                
            "ano_lectivo" => "required"             
            ]);
            if($validacao->fails()){ 
                return redirect()->back()->withErrors($validacao)->withInput();
            } 
                   
            $user = auth()->user();
            $modulo = Modulo::find($data['modulo']);            
            $disciplinas = $modulo->disciplinas()->get();            
            $turma = $modulo->turmas()->create($data);           
            // dd($turma);           
            $turma->disciplinas()->saveMany($disciplinas);
            // dd($disciplinas);
            // foreach ($disciplinas as $disciplina) {
            // $qtd = $turma->aulas()->where('disciplina_id',$disciplina->id)->count();                          
            //     for ($i=0; $i < intVal($disciplina->pivot->carga); $i++) {
            //         $aula = $turma->aulas()->create(
            //         ['nome'=>'Aula',
            //          'disciplina_id'=>$disciplina->id,
            //          'sala_id'=>$turma->sala_id
            //          ]
            //         );
                    
            //     }
            // }
            // dd($turma->disciplinas()->get());
               // foreach ($disciplinas as $disciplina) {
               // }            

        }else{
                       
            // $validacao = \Validator::make($data,[
            // "nome" => "required"         
            // ]);
            // if($validacao->fails()){
            //     return redirect()->back()->withErrors($validacao)->withInput();
            // }
            // dd($data);
            $user = auth()->user();
            $data['user_id'] = $user->id;                    
            $aluno = Aluno::find($data['aluno_id']);            
            $data['numero'] = Turma::qtdAlunos($data['turma_id']) + 1; 
            $turma = Turma::find($data['turma_id'])->alunos()->attach($data['aluno_id'],$data);
            // $turma->attach($user);
        }
    }
     
    public function show($id)
    {
       return Turma::find($id);
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
        // "curso" => "required",        
        // "classe" => "required"        
        // ]);
        // if($validacao->fails()){
        //     return redirect()->back()->withErrors($validacao)->withInput();
        // }
        $user = auth()->user();             
        $turma = Turma::find($id); 
        $turma->update($data);       
        $turma->user()->associate($user);
        $turma->save();
    
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
        $turma = Turma::find($id);
        Aula::where('turma_id',$turma->id)->delete();
        $turma->delete();
        return redirect()->back();
    }

    public function horario($id)
    {    
       $user = auth()->user();
       $epoca = Epoca::where('activo','S')->first();
       $turma = Turma::find($id);

       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>""]
    ]);      
       
       $listaModelo = ['nao_alocadas'=>Aula::aulas_nao_alocadas($turma->id),'alocadas'=>Aula::aulas_alocadas($turma->id)];
       // dd($listaModelo);      
       
       $listaDisciplinas = Modulo::listaDisciplinas(1,5);
       $listaModulos = Modulo::all();
       $listaCursos = Curso::all();
       $listaClasses = Classe::all();
       $ano_lectivo = $epoca->ano_lectivo; 
       return view('admin.turmas.horario.index',compact('listaMigalhas','listaModelo','listaCursos','listaClasses','user','listaDisciplinas','listaModulos','ano_lectivo','turma'));   

    }

    function horarioPDF($turma_id){
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_to_html($turma_id));      
      $pdf->set_paper('A4','potrait');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }   

    public function convert_to_html($id)
    {    
       $user = auth()->user();
       $epoca = Epoca::where('activo','S')->first();
       $turma = Turma::find($id);
       $sala = Sala::find($turma->sala_id);
       $modulo = Modulo::find($turma->modulo_id);
       $curso = Curso::find($modulo->curso_id);
       $classe = Curso::find($modulo->classe_id);
       $turma_info = explode(' ', $turma->nome);
       $listaDisciplinas = Turma::listaProfessorPorDisc($turma);
       

       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Turmas","url"=>""]
    ]);      
       
       $listaModelo = ['nao_alocadas'=>Aula::aulas_nao_alocadas($turma->id),'alocadas'=>Aula::aulas_alocadas($turma->id)];
       // dd($listaModelo);      
       
       // $listaDisciplinas = Modulo::listaDisciplinas(1,5);
       $listaModulos = Modulo::all();
       $listaCursos = Curso::all();
       $listaClasses = Classe::all();
       $ano_lectivo = date('Y'); 
       
       $titulos = [['','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],['07:00 - 07:50','08:00 - 08:50','09:00 - 09:50','10:00 - 10:50','11:00 - 11:50','12:00 - 12:50','13:00 - 13:50','14:00 - 14:50','15:00 - 15:50','16:00 - 16:50','17:00 - 17:50']];

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
         .ct {
          
         }
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
       #seccao_topo_grupo{
          position:relative;
          top:10px;
       }
       #seccao_topo_grupo > div{
          float:left;
          width:25%;
       }
       #seccao_topo_grupo > #seccao_5,#seccao_topo_grupo > #seccao_6,#seccao_topo_grupo > #seccao_7,#seccao_topo_grupo > #seccao_8{
          float:left;
          width:10%;
          margin-top:1px;
          font-size:10px;
       }
       #seccao_topo_grupo > #seccao_5>p,#seccao_topo_grupo > #seccao_6>p,#seccao_topo_grupo > #seccao_7>p,#seccao_topo_grupo > #seccao_8>p{
          margin-bottom:-12px;
       }
       #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-8px;
      }
       #rodape{;
          position:relative;          
          top:8%;
       }
       #rodape p{
         margin-top:-10px;
       }
       #rodape > #seccao_1{
          float:left;
          width:25%;
       }   
       #rodape > #seccao_2{
          float:right;
          width:25%;
       }       
       #mytable{
       width:100%;
       border-collapse:collapse;
       font-size:11px;

      }
       #tabela_area{
        background:transparent;
        position:relative;
        top:100px;
      }
      #mytable>th{
        text-align:center;
      }
      th,td{
        border:1px solid;
        padding: 3px;
        margin:5px;
      }

      </style>
      </head>
      <body onload='atribueCor()'>

      <div id='cabecalho' align='center' style='font-size: 11px;font-weight: bold;'' class='table-responsive text-uppercase'>
        <p>COLÉGIO PADRE BUILU</p>                           
        <p>FÉ E CIÊNCIA</p>                                       
        <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
        <p>PAUTA DE APROVEITAMENTO</p>            
        <p>ÁREA DE FORMAÇÃO: $curso->nome</p>           
        <p>REGIME DIURNO (<span>$turma->periodo</span>)</p> 
      </div>
      <div id='seccao_topo_grupo' align='center' style='font-size: 11px;'>
      <div id='seccao_1'>       
       <p>ANO LECTIVO $turma->ano_lectivo</p> 
      </div>
      <div id='seccao_2'>
       <p>TURMA: $turma->nome </p>         
      </div>
      <div id='seccao_3' style='width:30%'>
       <p>CURSO: $curso->nome</p>
      </div>      
      <div id='seccao_5' align='left'>       
       <p>$sala->nome</p>
      </div> 
    </div>
    <div id='tabela_area'>
     <table id='mytable'>
     <thead border:1px solid;>
     <tr style='font-weight: bold;'>";

      foreach($titulos[0] as $titulo){
      $output .="
      <th scope='col' class='centro'>$titulo</th>";
      }             
      $output .="</tr>
     </thead>
     <tbody>";
      $counter = 0;    
      array_shift($titulos[0]);  
      foreach($titulos[1] as $titulo){ 
      $counter2 = 0;
      $output .="
      <tr style='font-size:11px'>
      <th scope='col' class='centro'>$titulo</th>";      
      foreach ($titulos[0] as $titulo){           
             $output .="
             <td scope='col' class='centro' style='height:25px;'>";
             foreach ($listaModelo['alocadas'] as $item){
              if($item->dia == $titulo && $item->hora == $counter + 7){
                  $counter2++;  
                  $output .="
                  $item->disciplina<br/><i style font-size:3px>$item->sala<i/>";
                  //  $output .="
                  // $item->disciplina";
                 }
             }
             $output .="
             </td>";        
             }
             $output .="</tr>";
             $counter++;
             }             
             $output .="</tbody>
             </table>

     <div id='tabela_area2'style='margin-top:50px;'>
     <table id='mytable'>
     <thead border:1px solid;>
     <tr style='font-weight: bold;'>";

      
      $output .="
      <th scope='col' class='centro'>Disciplina</th>      
      <th scope='col' style='width:10%;' class='centro'>Acrónimo</th>      
      <th scope='col' style='width:30%;' class='centro'>Professor</th>
      <th scope='col' style='width:5%;' class='centro'>Carga</th>

      ";              
      $output .="</tr>
     </thead>
     <tbody>";
      $totalCarga = 0;
      foreach($listaDisciplinas as $disciplina){       
      $totalCarga += $disciplina->carga;
      $output .="
      <tr style='font-size:11px'>
      <td scope='col' >$disciplina->disciplina</td>
      <td scope='col' class='text-uppercase'>$disciplina->acronimo</td>
      <td scope='col' >$disciplina->professor</td>
      <td scope='col' class='centro'>$disciplina->carga</td></tr>
      ";       
     }
              
       $output .="
       <tr>
       <th scope='col' colspan = '3'  class='centro'>Total Dos Tempos</th>
       <td scope='col'  class='centro'>$totalCarga</td></tr></tbody>
       </table>
       </div>
          <div id='rodape' align='center' class='text-uppercase' style='font-size:11px'>
                <div id='seccao_1' align='center'>
                 <p>Cabinda, aos " . date('d').' de '.date('M').' de '.date('Y') . "</p>                 
                </div>        
                <div id='seccao_2' align='center'>
                 <p>O SUB-DIRECTOR PEDAGÓGICO</p>
                 <p>_________________________________</p>  
                 <p>ERNESTO TIGRE ISSAMBO</p>
                </div>                                                     
              </div>    
              </body>
          </html";

      return $output;
    }


    

}
