<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\curso;
use App\Professor;
use App\Turma;
use App\Aluno;
use App\Avaliacao;
use App\Epoca;
use App\Sala;
use App\Instituicao;
use App\Area;
use PDF;
use Dompdf\Dompdf;


class TurmaPautaController extends Controller
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
       // $disciplina = Disciplina::find($);
       $modulo = $turma->modulo()->first();       
       $curso = Curso::find($modulo->curso_id);
       $classe = Classe::find($modulo->classe_id);        
       $coordenador = Professor::find($curso->professor_id);  
       $director_turma = $turma->professores()->where('director','S')->first(); 
       $director_turma = $turma->professores()->where('director','S')->first(); 
       $epoca = Epoca::where('activo','S')->first();
       $alunos_m = Turma::listaAlunos2($turma->id,1000)->where('sexo','M')->count();

       
              
              
       $turma_info = explode(' ', $turma->nome);
            
       $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],       
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Pauta","url"=>""]
    ]);
       
      // $listaModelo = Turma::listaModelo(14);
       // dd($listaModelo);
       // $listaModelo = Turma::cotacaoTrimestral($turma_id,1,1,"I");       
       $newdata = collect([]);
       $lista = Turma::classificaoTrimestrais2($turma_id,$epoca->trimestre);
       // dd($lista);
       foreach ($lista as $alunos) {
         foreach ($alunos as $aluno) {
             $aluno->pull('idmatricula');
             $aluno->pull('idade');
             $aluno->pull('Genero');
             $newdata->push($aluno->all());             
          }       
       }              
       $listaModelo = collect([
          'data' => $newdata
        ]);             
       
       $listaCabecalho = $turma->listaDisciplinas($turma->id,100); 

       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();       
       $user = auth()->user();
       
       
       return view('admin.turmas.pautaPDF.index',compact('turma','disciplina','listaMigalhas','listaModelo','listadisciplinas','listaProfessores','user','listaCabecalho','curso','classe','director_turma','coordenador','turma_info','epoca'));

    }

    function pdf($turma_id){
      set_time_limit(120);
      $turma = Turma::find($turma_id);
      $director_turma = $turma->professores()->where('director','s')->first();
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_to_html($turma_id));      
      $pdf->set_paper('A3','landscape');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }

    function convert_to_html($turma_id){
      // $customer_data = $this->get_customer_data();
        // dd($);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $sala = Sala::find($turma->sala_id);       
       // $disciplina = Disciplina::find($);
       $modulo = $turma->modulo()->first();       
       $curso = Curso::find($modulo->curso_id);
       $classe = Classe::find($modulo->classe_id); 
       $coordenador = Professor::find($curso->professor_id);
       $director_instituicao = $curso->director_instituto_mae;

       $director_turma = $turma->professores()->where('director','s')->first(); 
       $epoca = Epoca::where('activo','S')->first();
       $instituicao = Instituicao::all()->first();  

              
       $turma_info = explode(' ', $turma->nome);
            
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Professores","url"=>""]
    ]);
       
      // $listaModelo = Turma::listaModelo(14);
       // dd($listaModelo);
       // $listaModelo = Turma::cotacaoTrimestral(1,1,1,"I");
       $listaModelo = Turma::classificaoTrimestrais2($turma_id,$epoca->trimestre);
                
        
       $listaCabecalho = ['Nº','Nº Mat','Nome','Idade'];
       $listaCabecalho2 = $turma->listaDisciplinas($turma->id,100);

       
       foreach ($listaCabecalho2 as $key => $value) {        
           array_push($listaCabecalho,$value->disciplina);
       }
        array_push($listaCabecalho,'Genero','Med','OBS');


       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();
       $totalAlunos = sizeof($listaModelo['data']);
       $alunos_m = Turma::listaAlunos2($turma->id,1000)->where('sexo','M')->count();
       $alunos_f = $totalAlunos - $alunos_m; 
       $aprovados = $listaModelo['data']->where('OBS','Transita');
       $reprovados = $listaModelo['data']->where('OBS','Não Transita');       
       $aprovados_m = $aprovados->where('Genero','M');
       $aprovados_f = $aprovados->where('Genero','F');
       $reprovados_m = $reprovados->where('Genero','M');
       $reprovados_f = $reprovados->where('Genero','F');  
       $desistidos = $listaModelo['data']->where('OBS','Desistido');
       $desistidos_m = $desistidos->where('Genero','M');
       $desistidos_f = $desistidos->where('Genero','F');
      

       /*QUANTIDADES*/
       
       $aprovados_qtd = $aprovados->count();
       $reprovados_qtd = $reprovados->count();      
       $aprovados_m_qtd = $aprovados_m->count();       
       $aprovados_f_qtd = $aprovados_f->count();
       $reprovados_m_qtd = $reprovados_m->count();
       $reprovados_f_qtd = $reprovados_f->count();
       $desistidos_qtd = $desistidos->count();
       $desistidos_m_qtd = $desistidos_m->count();
       $desistidos_f_qtd =  $desistidos_f->count();

       if(isset($director_turma)){
        $director_turma = $director_turma->nome;
       }else{
        $director = '';

       } 
        
       $user = auth()->user();

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
         .vtexto{
            -ms-transform: rotate(60deg); /* IE 9 */
            -webkit-transform: rotate(90deg); /* Safari 3-8 */
             transform: rotate(90deg);
             font-weight:bold;
             font-size:11px;            
             
         }
         .otexto{
            -ms-transform: rotate(30deg); /* IE 9 */
            -webkit-transform: rotate(30deg); /* Safari 3-8 */
             transform: rotate(30deg);
             font-weight:bold;
             font-size:11px;
             height:50px;
             width:1px;
             word-break: keep-all;
             
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
          width:20%;
       }
       #seccao_topo_grupo > #seccao_5,#seccao_topo_grupo > #seccao_6,#seccao_topo_grupo > #seccao_7,#seccao_topo_grupo > #seccao_8{
          float:left;
          width:5%;
          margin-top:-2px;
          font-size:10px;
       }
       #seccao_topo_grupo > #seccao_5>p,#seccao_topo_grupo > #seccao_6>p,#seccao_topo_grupo > #seccao_7>p,#seccao_topo_grupo > #seccao_8>p{
          margin-bottom:-12px;
       }
       #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-12px;
      }
       #rodape{;
          position:relative;          
          top:9%;
       }
       #rodape p{
         margin-top:-15px;
       }
       #rodape > div{
          float:left;
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
        top:80px;
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
        <p>PAUTA DE APROVEITAMENTO</p>            
        <p>ÁREA DE FORMAÇÃO: $curso->nome</p>           
        <p>REGIME DIURNO (<span>$turma->periodo</span>)</p> 
      </div>
      <div id='seccao_topo_grupo' align='center' style='font-size: 12px;'>
      <div id='seccao_1'>
       <p>A DIRECTOR(A) DO INSTITUTO </p> 
       <p>_________________________________</p> 
       <p class='text-uppercase'>$director_instituicao</p>
       <p>DATA " . date('d').'/'.date('m').'/'.date('Y') . "</p> 
      </div>
      <div id='seccao_2'>
       <p>ANO LECTIVO $turma->ano_lectivo</p> 
       <p>$classe->nome CLASSE TURMA: $turma_info[2] </p>         
      </div>
      <div id='seccao_3'>
       <p>CURSO: $curso->nome</p>
      </div>
      <div id='seccao_4'>
       <p>$epoca->trimestre  TRIMESTRE</p>
      </div>
      <div id='seccao_5' align='left'>
       <p>TOTAL: $totalAlunos</p>
       <p>M: $alunos_m</p>
       <p>F: $alunos_f</p>
       <p style='text-transform:uppercase;'>$sala->nome</p>
      </div>
      <div id='seccao_6' align='left'>
       <p>Aptos: $aprovados_qtd</p>
       <p>M: $aprovados_m_qtd</p>
       <p>F: $aprovados_f_qtd</p>       
      </div>
      <div id='seccao_7' align='left'>
       <p>N/Aptos: $reprovados_qtd</p>
       <p>M: $reprovados_m_qtd</p>
       <p>F: $reprovados_f_qtd</p>        
      </div>
      <div id='seccao_8' align='left'>
       <p>Desistidos: $desistidos_qtd</p>
       <p>M: $desistidos_m_qtd</p>
       <p>F: $desistidos_f_qtd</p>         
      </div>
    </div>
    <div id='tabela_area'>
     <table id='mytable'>
     <thead border:1px solid;>
     <tr style='font-weight: bold;'>
      <th class='centro' scope='col' rowspan='3' >Nº</th>  
      <th scope='col' rowspan='3'style='width:5%;'><div class='otexto' style='font-size:10px;'>Nº_MATRÍCULA</div></th></th>  
      <th scope='col' rowspan='3'style='width:15%;'><span style='font-size:14;padding-left:5px;'>Nome</span></th>  
      <th class='centro' scope='col' rowspan='3'><p class='vtexto'>IDADE</p></th>";

      foreach($listaCabecalho2 as $key => $value){
      $output .="
      <th scope='col' colspan='3'class='centro'>$value->acronimo</th>";
      }
      $output .="
      <th style='width:0.1%' scope='col' rowspan='3' class='centro'><div class='vtexto'>GÊNERO</div></th>  
      <th style='width:0.1%' scope='col' rowspan='3' class='centro'><div class='vtexto'>MÉDIA</div></th>  
      <th style='width:5%' scope='col' rowspan='3' class='centro'>OBS</th>  
    </tr>
    <tr>";
      $ct = '';
      if($epoca->trimestre == 'I'){
        $ct = 'CT1';
      }else if($epoca->trimestre == 'II'){
        $ct = 'CT2';
      }else{
        $ct = 'CT3';
      }
      foreach($listaCabecalho2 as $key => $value){
      $output .="
      <th scope='col' colspan='2' class='centro'>Faltas</th>  
      <th scope='col' rowspan='2' class='centro'>$ct</th>
      ";
      }
      $output .="          
    </tr>
    <tr>";
      foreach($listaCabecalho2 as $key => $value){
      $output .="<th scope='col' class='centro'>J</th>  
      <th scope='col' class='centro'>N</th>";      
      }         
      $output .="</tr>
     </thead>
     <tbody>";
      foreach($listaModelo['data'] as $key=>$aluno){    
                
      $count = 0;    
      $fundo = '';      
      $vermelhado = '';
      $status = $aluno['OBS'];
      if($status == 'Desistido' || $status == 'Suspenso' || $status == 'Transferido'){
          $status=='Desistido'? $fundo = 'yellow':($status=='Suspenso'? $fundo = 'rgb(255,212,227)' : $fundo ='rgb(247,221,252)');
            
      }
      $ct = 7;

      $output .="<tr style='font-size:10px;background:$fundo;'>";
       foreach($aluno as $value){    
        $count++;
        $cor = 'black';        
        
        if($count == $ct){         
            if($value != '' & $value == 'Desistido' || $value == 'Suspenso' || $value == 'Transferido' || $value == 'Não Transita'){              
              $cor = 'red';              
            }else if($value != '' & $value == 'Transita'){
              $cor = 'black';                 
            }else if($value != '' & floatval($value) < 10){
            $cor = 'red'; 
           // dd($value);
           }
        $output .="<td class='ct' style='text-align:center;font-weight:bold;'>
                    <span style='color:$cor;'>$value</span>
                   </td>";          
        $ct +=3; 
        }else if($count==sizeof($aluno)-1){
           floatval($value) < 11 ? $cor = 'red':$cor = 'black';

          $output .="<td style='text-align:center;font-weight:bold;'>
                    <span style='color:$cor;'>$value</span>
                   </td>";          
          }else if($count==sizeof($aluno)-2 || $count==1 || $count==4){
            $output .="<td class='centro'>$value</td>";
          }else{
            $output .="<td>$value</td>";
          }

        
        }
      $output .="</tr>";    
      }
          
    
  $output .="</tbody>
</table>
</div>
<div id='rodape' align='center' class='text-uppercase'>
      <div id='seccao_1'>
       <p>O(A) DIRECTOR(A) DE TURMA                  
       <p>_________________________________</p>

       <p>$director_turma</p> 
      </div>
      <div id='seccao_2'>
       <p>O COORDENADOR DO CURSO</p> 
       <p>_________________________________</p> 
       <p>$coordenador->nome</p>         
      </div>
      <div id='seccao_3'>
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>$instituicao->director_pedagogico</p>
      </div>
      <div id='seccao_4'>
       <p> O DIRECTOR DO COLÉGIO</p>
       <p>_________________________________</p>  
       <p>$instituicao->director_instituicao</p>              
      </div>                                                  
    </div>    
    </body>
</html";

      return $output;
    }

    
    

    function pautafinalpdf($turma_id){
      set_time_limit(120);
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->pauta_final_html($turma_id));      
      $pdf->set_paper('A3','landscape');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }    
    function pauta_final_html($turma_id){
      // $customer_data = $this->get_customer_data();
        // dd($);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);       
       // $disciplina = Disciplina::find($);
       $sala = Sala::find($turma->sala_id);       
       $modulo = $turma->modulo()->first();
       $curso = Curso::find($modulo->curso_id);
       $classe = Classe::find($modulo->classe_id); 
       $area_formacao = Area::find($curso->area_id); 
       $coordenador = Professor::find($curso->professor_id);  
       $director_turma = $turma->professores()->where('director','s')->first(); 
       $epoca = Epoca::where('activo','S')->first();
       $instituicao = Instituicao::all()->first();       
       $director_instituicao = $curso->director_instituto_mae;

       if(is_null($director_instituicao)){
       $director_instituicao = $instituicao->director_instituicao;
       }  
       
                
              
       $turma_info = explode(' ', $turma->nome);       
            
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Professores","url"=>""]
    ]);
       
      // $listaModelo = Turma::listaModelo(14);
       // dd($listaModelo);
       // $listaModelo = Turma::cotacaoTrimestral(1,1,1,"I");
      
       $listaModelo = Turma::classificaoAnual($turma_id,'III');               
        
       $listaCabecalho = ['Nº','Nº Mat','Nome','Idade'];
       $listaCabecalho2 = $turma->listaDisciplinasCurriculares($turma->modulo_id,30);
       $modulo_nome = explode('ª', $modulo->nome);
       $modulo_nome = explode(' ', $modulo_nome[0]);  
     
       
      if($classe->nome == '13ª'){        

        /*DISCIPLINAS 12*/        
        $modulo_12 = Modulo::where('nome',$modulo_nome[0] . ' ' .
        intVal($modulo_nome[1] - 1) . 'ª')->first(); 
        
        $disciplinas_13 = $modulo->disciplinas()->get();
        $disciplinas_12 = $modulo_12->disciplinas()->get();
        $disc_terminadas_12 = $modulo_12->disciplinas()->where('terminal','S')->where('curricular','S')->get()->reverse(); 

        foreach ($disc_terminadas_12 as $disc_terminada) {
         $listaCabecalho2->prepend($disc_terminada);
       } 
      }

       if($classe->nome == '12ª' || $classe->nome == '13ª'){        

        /*DISCIPLINAS 11*/   
        $retrocesso = 1;
        $classe->nome == '13ª' ? $retrocesso = 2 : $retrocesso;         
        $modulo_11 = Modulo::where('nome',$modulo_nome[0] . ' ' .
        intVal($modulo_nome[1] - $retrocesso) . 'ª')->first();     
        $disciplinas_11 = $modulo_11->disciplinas()->get();
        $disc_terminadas_11 = $modulo_11->disciplinas()->where('terminal','S')->where('curricular','S')->get()->reverse();  

        foreach ($disc_terminadas_11 as $disc_terminada) {
         $listaCabecalho2->prepend($disc_terminada);
       } 
      }

       if($classe->nome == '11ª' || $classe->nome == '12ª' || $classe->nome == '13ª'){ 

        /*DISCIPLINAS 10*/
        $retrocesso = 1;
        $classe->nome == '12ª' ? $retrocesso = 2 : ($classe->nome == '13ª'? $retrocesso = 3 : 
        $retrocesso);         
        $modulo_10 = Modulo::where('nome',$modulo_nome[0] . ' ' .
        intVal($modulo_nome[1] - $retrocesso) . 'ª')->first();         

        $disciplinas_10 = $modulo_10->disciplinas()->get();
        $disc_terminadas_10 = $modulo_10->disciplinas()->where('terminal','S')->where('curricular','S')->get()->reverse(); 

        foreach ($disc_terminadas_10 as $disc_terminada) {
         $listaCabecalho2->prepend($disc_terminada);
       }

       }     

       $listadisciplinas = Disciplina::orderBy('nome')->get();       
       $listaProfessores = Professor::orderBy('nome')->get();
       $totalAlunos = sizeof($listaModelo['data']); 
       $alunos_m = Turma::listaAlunos2($turma->id,1000)->where('sexo','M')->count();
       $alunos_f = $totalAlunos - $alunos_m; 
       $aprovados = $listaModelo['data']->where('Result','Trans.');
       $reprovados = $listaModelo['data']->where('Result','n/Trans.');
       $aprovados_m = $aprovados->where('Genero','M');
       $aprovados_f = $aprovados->where('Genero','F');
       $reprovados_m = $reprovados->where('Genero','M');
       $reprovados_f = $reprovados->where('Genero','F');  
       $desistidos = $listaModelo['data']->where('Result','Desistido');
       $desistidos_m = $desistidos->where('Genero','M');
       $desistidos_f = $desistidos->where('Genero','F');

       /*QUANTIDADES*/
       
       $aprovados_qtd = $aprovados->count();
       $reprovados_qtd = $reprovados->count();      
       $aprovados_m_qtd = $aprovados_m->count();       
       $aprovados_f_qtd = $aprovados_f->count();
       $reprovados_m_qtd = $reprovados_m->count();
       $reprovados_f_qtd = $reprovados_f->count();
       $desistidos_qtd = $desistidos->count();
       $desistidos_m_qtd = $desistidos_m->count();
       $desistidos_f_qtd =  $desistidos_f->count();
       
       $disciplinas_terminais = $modulo->disciplinas()->where('terminal','S')->where('curricular','S')->get();
       if(isset($director_turma)){
        $director_turma = $director_turma->nome;
       }else{
        $director = '';

       } 

               
       $user = auth()->user();

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
          width:20%;
       }
       #seccao_topo_grupo > #seccao_5,#seccao_topo_grupo > #seccao_6,#seccao_topo_grupo > #seccao_7,#seccao_topo_grupo > #seccao_8{
          float:left;
          width:5%;
          margin-top:-2px;
          font-size:10px;
       }
       #seccao_topo_grupo > #seccao_5>p,#seccao_topo_grupo > #seccao_6>p,#seccao_topo_grupo > #seccao_7>p,#seccao_topo_grupo > #seccao_8>p{
          margin-bottom:-12px;
       }
       #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-12px;
      }
       #rodape{;
          position:relative;          
          top:9%;
       }
       #rodape p{
         margin-top:-15px;
       }
       #rodape > div{
          float:left;
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
        top:80px;
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
        <p>PAUTA DE APROVEITAMENTO</p>            
        <p>ÁREA DE FORMAÇÃO: $area_formacao->nome</p>           
        <p>REGIME DIURNO (<span>$turma->periodo</span>)</p> 
      </div>
      <div id='seccao_topo_grupo' align='center' style='font-size: 12px;'>
      <div id='seccao_1'>
       <p>DIRECTOR(A) DO INSTITUTO </p> 
       <p>_________________________________</p> 
       <p>$director_instituicao</p>
       <p>DATA " . date('d').'/'.date('m').'/'.date('Y') . "</p> 
      </div>
      <div id='seccao_2'>
       <p>ANO LECTIVO $turma->ano_lectivo</p> 
       <p>$classe->nome CLASSE TURMA: $turma_info[2] </p>         
      </div>
      <div id='seccao_3'>
       <p>CURSO: $curso->nome</p>
      </div>
      <div id='seccao_4'>
       <p>$epoca->trimestre  TRIMESTRE</p>
      </div>
      <div id='seccao_5' align='left'>
       <p>TOTAL: $totalAlunos</p>
       <p>M: $alunos_m</p>
       <p>F: $alunos_f</p>
       <p style='text-transform:uppercase'>$sala->nome</p>
      </div>
      <div id='seccao_6' align='left'>
       <p>Aptos: $aprovados_qtd</p>
       <p>M: $aprovados_m_qtd</p>
       <p>F: $aprovados_f_qtd</p>       
      </div>
      <div id='seccao_7' align='left'>
       <p>N/Aptos: $reprovados_qtd</p>
       <p>M: $reprovados_m_qtd</p>
       <p>F: $reprovados_f_qtd</p>        
      </div>
      <div id='seccao_8' align='left'>
       <p>Desistidos: $desistidos_qtd</p>
       <p>M: $desistidos_m_qtd</p>
       <p>F: $desistidos_f_qtd</p>         
      </div>
    </div>


    <div id='tabela_area'>
     <table id='mytable'>
     <thead border:1px solid;>
     <tr style='font-weight: bold;'>
     <th class='centro' scope='col' rowspan='3' >Nº</th>  
      <th scope='col' rowspan='3'style='width:1%;'>
         <p class='otexto' style='font-size:10px;'>Nº_MATR.</p>
      </th>  
      <th scope='col' rowspan='3'style='width:15%;'>
        <span style='font-size:14;padding-left:5px;'>NOME</span>
      </th>  
      <th class='centro' scope='col' rowspan='3'>
        <p class='vtexto'>IDADE</p>
      </th>";
      foreach($listaCabecalho2 as $key => $value){
        
         /*10ª CLASSE*/
        if($classe->nome == "10ª"){

         if($disciplinas_terminais->contains($value->id)){                       
         $output .="
          <th style='font-size:12px;font-weight:bold;'scope='col' colspan='7'class='centro'>$value->acronimo</th>";          
         }else if(isset($disc_terminadas_10) && $disc_terminadas_10->contains($value->id)){ 
          $output .="
          <th scope='col' colspan='1'class='centro'>$value->acronimo</th>";         
         }else{
          $output .="
          <th scope='col' colspan='5'class='centro'>$value->acronimo</th>";
         }
        
        /*11ª CLASSE*/
        }else if($classe->nome == "11ª"){
          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_anterior = $disciplinas_10->where("id",$value->id);          

          /*DISCIPLINA TERMINADA*/
          if($disc_terminada_10->isNotEmpty()){ 
          $output .="
          <th scope='col' colspan='1' rowspan='3' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>$value->acronimo</p></th>";
          
          /*DISCIPLINA TERMINAL COM ANTECEDENTE*/
         }else if($disc_terminal->isNotEmpty() && $disc_anterior->isNotEmpty()){            
         $output .="
          <th scope='col' colspan='8'class='centro'>$value->acronimo</th>";
          
          /*DISCIPLINA TERMINAL SEM ANTECEDENTE*/
         }else if($disc_terminal->isNotEmpty() && $disc_anterior->isEmpty()){ 
         $output .="
          <th scope='col' colspan='7'class='centro'>$value->acronimo</th>";
          
          /*DISCIPLINA DE CONTINUIDADE COM ANTECEDENTE*/
         }else if($disc_terminal->isEmpty() && $disc_anterior->isNotEmpty()){ 

         $output .="
          <th scope='col' colspan='6'class='centro'>$value->acronimo</th>";
         }else{
          /*DISCIPLINA DE CONTINUIDADE SEM ANTECEDENTE*/
          $output .="
          <th scope='col' colspan='5'class='centro'>$value->acronimo</th>";
         }  
          /*12ª CLASSE*/
        }else if($classe->nome == "12ª"){
          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          ($disciplinas_terminais);
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
          
          $disc_anterior = $disciplinas_11->where("id",$value->id);         
          $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
          $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
          $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count());

          /*DISCIPLINA TERMINADA*/

         if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty()){ 
          $output .="
          <th scope='col' colspan='1' rowspan='3' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>$value->acronimo</p></th>";    
         /*DISCIPLINA TERMINAL COM ANTECEDENTE*/
         }else if($disc_terminal->isNotEmpty() && $disc_anterior->isNotEmpty()){          
          if($qtd == 2){            
          $output .="
          <th scope='col' colspan='9'class='centro'>$value->acronimo</th>";
          }else{
          $output .="
          <th scope='col' colspan='8'class='centro'>$value->acronimo</th>";
          }            
          
          /*DISCIPLINA TERMINAL SEM ANTECEDENTE*/
         }else if($disc_terminal->isNotEmpty() && $disc_anterior->isEmpty()){ 
         $output .="
          <th scope='col' colspan='7'class='centro'>$value->acronimo</th>";
          
          /*DISCIPLINA DE CONTINUIDADE COM ANTECEDENTE*/
         }else if($disc_terminal->isEmpty() && $disc_anterior->isNotEmpty()){ 

          if($qtd == 2){
            $output .="
            <th scope='col' colspan='7'class='centro'>$value->acronimo</th>"; 
          }else{
            $output .="
            <th scope='col' colspan='6'class='centro'>$value->acronimo</th>";            

          }
         }else{
          /*DISCIPLINA DE CONTINUIDADE SEM ANTECEDENTE*/
          $output .="
          <th scope='col' colspan='5'class='centro'>$value->acronimo</th>";
         }  
        }else if($classe->nome == "13ª"){
          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          ($disciplinas_terminais);
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
          $disc_terminada_12 = $disc_terminadas_12->where("id",$value->id);          
          
          $disc_anterior = $disciplinas_12->where("id",$value->id);         
          $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
          $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
          $disc_anterior_12 = $disciplinas_12->where("id",$value->id);          
          $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count() + 
          $disc_anterior_11->count());

          /*DISCIPLINA TERMINADA*/

         if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty() || $disc_terminada_12->isNotEmpty()){ 
          $output .="
          <th scope='col' colspan='1' rowspan='3' class='centro' style='font-size:5px'>$value->acronimo</th>";    
         /*DISCIPLINA TERMINAL COM ANTECEDENTE*/
         }else if($disc_terminal->isNotEmpty() && $disc_anterior->isNotEmpty()){          
          if($qtd == 3){            
          $output .="
          <th scope='col' colspan='10'class='centro'>$value->acronimo</th>";
          }else if($qtd == 2){            
          $output .="
          <th scope='col' colspan='9'class='centro'>$value->acronimo</th>";
          }else{
          $output .="
          <th scope='col' colspan='8'class='centro'>$value->acronimo</th>";
          }            
          
          /*DISCIPLINA TERMINAL SEM ANTECEDENTE*/
         }else if($disc_terminal->isNotEmpty() && $disc_anterior->isEmpty()){ 
         $output .="
          <th scope='col' colspan='7'class='centro'>$value->acronimo</th>";
          
          /*DISCIPLINA DE CONTINUIDADE COM ANTECEDENTE*/
         }else if($disc_terminal->isEmpty() && $disc_anterior->isNotEmpty()){ 

          if($qtd == 3){
            $output .="
            <th scope='col' colspan='8'class='centro'>$value->acronimo</th>"; 
          }if($qtd == 2){
            $output .="
            <th scope='col' colspan='7'class='centro'>$value->acronimo</th>"; 
          }else{
            $output .="
            <th scope='col' colspan='6'class='centro'>$value->acronimo</th>";
          }
         }else{
          /*DISCIPLINA DE CONTINUIDADE SEM ANTECEDENTE*/
          $output .="
          <th scope='col' colspan='5'class='centro'>$value->acronimo</th>";
         }  
        }


      }
      $output .="
      <th scope='col' rowspan='3' class='centro'><p class='vtexto' style='margin:20px -18px;'>GÊNERO</p></th>  
      <th scope='col' rowspan='3' class='centro'><p class='vtexto' style='margin:0px -13.5px;'>MÉDIA</p></th>  
      <th scope='col' rowspan='3' class='centro'>OBS</th>  
      <th scope='col' rowspan='3' class='centro'>RESUL.</th>  
    </tr>
    <tr>";
      
      foreach($listaCabecalho2 as $key => $value){
        /*10º CLASSE*/
        if($classe->nome == '10ª'){
       
        $output .="
        <th scope='col' colspan='2' class='centro'>F</th>  
        <th scope='col' rowspan='2' class='centro'>CF</th>
        <th scope='col' rowspan='2' class='centro'>PG</th>
        <th scope='col' rowspan='2' class='centro'><p style='font-size:11px;' class='vtexto'>CA10ª</p></th>
        ";
      
        if($disciplinas_terminais->contains($value->id)){                       
        $output .="
        <th scope='col' rowspan='2' class='centro'><p style='font-size:11px;margin:0px -15px;' class='vtexto'>EXAME</p></th>
        <th scope='col' style='font-size:8px' rowspan='2' class='centro'><p style='font-size:11px;' class='vtexto'>CFD</p></th>
        ";
         }

         /*11º CLASSE*/
        }else if($classe->nome == '11ª'){

          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_anterior_10 = $disciplinas_10->where("id",$value->id);
          
        if($disc_terminada_10->isNotEmpty()){         
        
        }else if($disc_anterior_10->isNotEmpty()){                       
        $output .="
        <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA10ª</p></th>";
         }
        if($disc_terminada_10->isEmpty()){
        $output .="
        <th scope='col' colspan='2' class='centro'>F</th>  
        <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>CF</p></th>
        <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>PG</p></th>
        <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA$modulo_nome[1]ª</p></th>
        "; 
        }
        if($disc_terminal->isNotEmpty()){                       
        $output .="
        <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:20px -15px;font-size:11px;'>EXAME</p></th>
        <th scope='col'rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;font-size:11px;'>CFD</p></th>
        ";
         }

        }else if($classe->nome == '12ª'){

          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          ($disciplinas_terminais);
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
                   
          
          $disc_anterior = $disciplinas_11->where("id",$value->id);         
          $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
          $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
          $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count());
          
        if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty()){                
        
        }else if($disc_anterior->isNotEmpty()){
        if($qtd == 2){
            $output .="
           <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA10ª</p></th>
            <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA11ª</p></th>";

        }else if($qtd == 1){
            $output .="
            <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA11ª</p></th>"; 
        }

        }
        if($disc_terminada_10->isEmpty() && $disc_terminada_11->isEmpty()){                      
         
        $output .="

        <th scope='col' colspan='2' class='centro'>F</th>  
        <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>CF</p></th>
        <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>PG</p></th>
        <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA$modulo_nome[1]ª</p></th>
        "; 
        }

        if($disc_terminal->isNotEmpty()){                       
        $output .="
        
        <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:20px -15px;font-size:11px;'>EXAME</p></th>
        <th scope='col'rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;font-size:11px;'>CFD</p></th>
        ";
         }

        }else if($classe->nome == '13ª'){

          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          ($disciplinas_terminais);
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
          $disc_terminada_12 = $disc_terminadas_11->where("id",$value->id);          
          
          $disc_anterior = $disciplinas_12->where("id",$value->id);         
          $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
          $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
          $disc_anterior_12 = $disciplinas_12->where("id",$value->id);
          $disciplina_13 = $disciplinas_13->where("id",$value->id);
          $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count() + $disc_anterior_12->count());
          
         
        if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty() || 
          $disc_terminada_12->isNotEmpty()){                
        
        }else if($disc_anterior->isNotEmpty() && $disciplina_13->isNotEmpty()){
        if($qtd == 3){
            $output .="
            <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C10</th>
            <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C11</th>
            <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C12</th>
            ";
        }if($qtd == 2){
            $output .="
            <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C11</th>
            <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C12</th>";
        }else if($qtd == 1){
            $output .="
            <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C12</th>"; 
        }

        }
        if((($disc_terminada_10->isEmpty() && $disc_terminada_11->isEmpty()) && 
                  $disc_terminada_12->isEmpty()) && $disciplina_13->isNotEmpty()){      
        $output .="
        <th scope='col' colspan='2' class='centro'>F</th>  
        <th scope='col' rowspan='2' class='centro'>CF</th>
        <th scope='col' rowspan='2' class='centro'>PG</th>
        <th scope='col' rowspan='2' class='centro' style='font-size:6px'>C$modulo_nome[1]</th>
        ";
        }

        if($disc_terminal->isNotEmpty()){                       
        $output .="
        <th scope='col' rowspan='2' class='centro'>E</th>
        <th scope='col' style='font-size:5px' rowspan='2' class='centro'>CFD</th>
        ";
         }

        }

        
      } 
      $output .="          
    </tr>
    <tr>";
      foreach($listaCabecalho2 as $key => $value){
        if($classe->nome == '10ª'){           
          $output .="<th scope='col' class='centro'>J</th>  
          <th scope='col' class='centro'>I</th>";
       
        }else if($classe->nome == '11ª'){

          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_anterior = $disciplinas_10->where("id",$value->id);

          if($disc_terminada_10->isNotEmpty()){         
          }else{ 
            $output .="<th scope='col' class='centro'>J</th>  
            <th scope='col' class='centro'>I</th>";
          }
        }else if($classe->nome == '12ª'){

          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
          $disc_anterior = $disciplinas_11->where("id",$value->id);

          if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty()){
                   
          }else{            
            $output .=
            "<th scope='col' class='centro'>J</th>  
            <th scope='col' class='centro'>I</th>";
          }
        }else if($classe->nome == '13ª'){

          $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
          $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
          $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
          $disc_terminada_12 = $disc_terminadas_11->where("id",$value->id);          
          $disc_anterior = $disciplinas_11->where("id",$value->id);
          $disciplina_13 = $disciplinas_13->where("id",$value->id);

          if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty() || $disc_terminada_12->isNotEmpty()){
                   
          }else if($disciplina_13->isNotEmpty()){                  
            $output .=
            "<th scope='col' class='centro'>J</th>  
            <th scope='col' class='centro'>I</th>";
          }else{

          }
        }
        
      }         
      $output .="</tr>
     </thead>
     <tbody>";
      foreach($listaModelo['data'] as $key=>$aluno){   
               
      $count = 0;    
      $fundo = '';      
      $vermelhado = '';
      $status = $aluno['Result'];      
      if($status == 'Desistido' || $status == 'Suspenso' || $status == 'Transferido'){
          $status=='Desistido'? $fundo = 'rgb(249,239,184)':($status=='Suspenso'? $fundo = 'rgb(255,212,227)' : $fundo ='rgb(247,221,252)');
            
      }
      $ct = 9;

      $output .="<tr style='font-size:10px;background:$fundo;'>";
       foreach($aluno as $key=>$value){    
        $count++;
        $cor = 'black';  
        $key = explode('_', $key);
        
        if($key[0] == 'Result'){         
            if($value != '' & $value == 'Desistido' || $value == 'Suspenso' || $value == 'Transferido' || $value == 'N/Trans.'){              
              $cor = 'red';              
            }else if($value != '' & $value == 'Trans.'){
              $cor = 'black';                 
            }else if($value != '' & floatval($value) < 10){
            $cor = 'red'; 
           // dd($value);
           }
        $output .="<td class='ct' style='text-align:center;'>
                    <span style='color:$cor;'>$value</span>
                   </td>";          
        $ct +=3; 
        }else if($key[0] == 'Media'){
           floatval($value) < 10 ? $cor = 'red':$cor = 'black';

          $output .="<td class='centro'>
                    <span style='color:$cor;'>$value</span>
                   </td>";          
          }else if($key[0] == 'ca'){
            floatval($value) < 10 ? $cor = 'red':$cor = 'black';
            $output .="<td class='centro'>
                    <span style='color:$cor;'>$value</span>
                    </td>";
          
          }else if($key[0] == 'pg'){
            floatval($value) < 4 ? $cor = 'red':$cor = 'black';
            $output .="<td class='centro'>
                    <span style='color:$cor;'>$value</span>
                    </td>";                    
          }else if($key[0] == 'cf'){
            floatval($value) < 6 ? $cor = 'red':$cor = 'black';
            $output .="<td class='centro'>
                    <span style='color:$cor;'>$value</span>
                    </td>";
          }else if($key[0] == 'cfd'){
            floatval($value) < 10 ? $cor = 'red':$cor = 'black';
            $output .="<td class='centro'>
                    <span style='color:$cor;'>$value</span>
                    </td>";
          }else if($key[0] == 'exame'){
            floatval($value) < 10 ? $cor = 'red':$cor = 'black';
            $output .="<td class='centro'>
                    <span style='color:$cor;'>$value</span>
                    </td>";
          }else if($key[0] == 'Genero' || $key[0] == 'numero' || $key[0] == 'idade'){           
            $output .="<td class='centro'>
                    <span>$value</span>
                    </td>";
          }else if($key[0] == 'ca'){                     
            $output .="<td class='centro'>
                    <span>$value</span>
                    </td>";
          }else if($key[0] == 'idmatricula'){           
            $output .="<td>
                    <span style='font-size:6px;'>$value</span>
                    </td>";
          }else if($key[0] == 'OBS'){           
            $output .="<td>
                    <span style='font-size:5px;color:red;'>$value</span>
                    </td>";
          }          else{
            $output .="<td>$value</td>";
          }

        
        }
      $output .="</tr>";    
      }
          
    
  $output .="</tbody>
</table>
</div>
<div id='rodape' align='center' class='text-uppercase'>
      <div id='seccao_1'>
       <p>O(A) DIRECTOR(A) DE TURMA                  
       <p>_________________________________</p> 
       <p>$director_turma</p> 
      </div>
      <div id='seccao_2'>
       <p>O COORDENADOR DO CURSO</p> 
       <p>_________________________________</p> 
       <p>$coordenador->nome</p>         
      </div>
      <div id='seccao_3'>
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>$instituicao->director_pedagogico</p>
      </div>
      <div id='seccao_4'>
       <p> O DIRECTOR DO COLÉGIO</p>
       <p>_________________________________</p>  
       <p>$instituicao->director_instituicao</p>       
      </div>                                                  
    </div>    
    </body>
</html";

      return $output;
    }






/*BOLENTIM DE NOTAS TRIMESTRAIS*/







function ficha_de_aproveitamento($turma_id){
      set_time_limit(240);
      $turma = Turma::find($turma_id);
      $director_turma = $turma->professores()->where('director','s')->first();
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_ficha_de_aproveitamento_html($turma_id));      
      $pdf->set_paper('A4','portrait');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }

    function convert_ficha_de_aproveitamento_html($turma_id){
      // $customer_data = $this->get_customer_data();
        // dd($);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $sala = Sala::find($turma->sala_id);       
       // $disciplina = Disciplina::find($);
       $modulo = $turma->modulo()->first(); 
       $disciplinas = $modulo->disciplinas()->get();          
       $alunos = Turma::listaAlunos($turma->id,100);              
       $curso = Curso::find($modulo->curso_id);
       $classe = Classe::find($modulo->classe_id); 
       $coordenador = Professor::find($curso->professor_id);
       $director_instituicao = $curso->director_instituto_mae;

       $director_turma = $turma->professores()->where('director','s')->first(); 
       $epoca = Epoca::where('activo','S')->first();
       $instituicao = Instituicao::all()->first();  

              
       $turma_info = explode(' ', $turma->nome);
            
       $listaMigalhas = json_encode([
        ["titulo"=>"Turmas","url"=>route('turmas.index')],       
        ["titulo"=>"Professores","url"=>""]
    ]);       
        
        $listaModelo = Turma::avaliacaoTrimestraisDaTurma($turma_id);
        $lista = collect([]);
        $elements = ['I TRIM NOTAS'=>'I TRIM NOTAS',
                     'I TRIM FALTAS'=>'I TRIM FALTAS',
                     'II TRIM NOTAS'=>'II TRIM NOTAS',
                     'II TRIM FALTAS'=>'II TRIM FALTAS',
                     'TOTAL FALTAS'=>'TOTAL FALTAS',
                     'CARGA HORÁRIA'=>'CARGA HORÁRIA',
                     'ESTADO'=>'ESTADO'
                    ];

          foreach ($alunos as $aluno){
            $avaliacao_aluno_collection = collect([]);
            $aluno_avaliacao = $listaModelo->where('aluno_id',$aluno->id);              
            $avaliacao_aluno_collection->put('nome',$aluno->nome);
            $avaliacao_aluno_collection->put('numero',$aluno->numero);
            
             foreach ($elements as $key => $element) {
                $data = collect([]);
                $cat_avaliacao = collect([]);
                $cat_avaliacao->put($key .'_'. $aluno->id,$element);
                $total = 0;
                

                 foreach($disciplinas as $disciplina){                                        
                    $aluno_avaliacao_aluno_disc = $aluno_avaliacao->where('disciplina_id',$disciplina->id)->first();

                    if(!is_null($aluno_avaliacao_aluno_disc)){
                       if($key == 'I TRIM NOTAS'){
                          $element = $aluno_avaliacao_aluno_disc->ct1b;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;
                       }else if($key == 'I TRIM FALTAS'){
                          $element = $aluno_avaliacao_aluno_disc->fnj1;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;

                       }else if($key == 'II TRIM NOTAS'){
                          $element = $aluno_avaliacao_aluno_disc->ct2b;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;

                       }else if($key == 'II TRIM FALTAS'){
                          $element = $aluno_avaliacao_aluno_disc->fnj1;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;

                       }else if($key == 'TOTAL FALTAS'){
                          $f1 = $avaliacao_aluno_collection['I TRIM FALTAS']['I TRIM FALTAS_' . $disciplina->acronimo];
                          $f2 = $avaliacao_aluno_collection['II TRIM FALTAS']['II TRIM FALTAS_' . $disciplina->acronimo];
                          $f1 = $f1 != null ? $f1 : 0;
                          $f2 = $f2 != null ? $f2 : 0;
                          $total = $f1 + $f2;
                          $total = $total != 0 ? $total : '';                   
                          $data->put($key . '_' . $disciplina->acronimo,$total);                          

                       }else if($key == 'CARGA HORÁRIA'){
                          $value = $disciplina->pivot->carga;                                             
                          $data->put($key . '_' . $disciplina->acronimo,$value);                         
                          

                       }else if($key == 'ESTADO'){
                          $total_faltas = $avaliacao_aluno_collection['TOTAL FALTAS']['TOTAL FALTAS_' . $disciplina->acronimo];
                          $total_faltas = $total_faltas != null ? $$total_faltas : 0;
                          $carga = $disciplina->pivot->carga;
                          $estado = $total_faltas >= $carga * 3 ? 'EXC.' : 'MANT.';
                          $data->put($key . '_' . $disciplina->acronimo,$estado);                        

                       }
                                                     
                    }else{
                      if($key == 'CARGA HORÁRIA'){
                          $value = $disciplina->pivot->carga;                   
                          $data->put($key . '_' . $disciplina->acronimo,$value);
                          
                       }else{
                          $data->put($key . '_' . $disciplina->acronimo,'');

                       }

                        if($key == 'I TRIM FALTAS'){
                          $element = 10;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;
                        }
                        if($key == 'II TRIM FALTAS'){
                          $element = 10;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;
                        }

                        if($key == 'TOTAL FALTAS'){
                          $f1 = $avaliacao_aluno_collection['I TRIM FALTAS']['I TRIM FALTAS_' . $disciplina->acronimo];
                          $f2 = $avaliacao_aluno_collection['II TRIM FALTAS']['II TRIM FALTAS_' . $disciplina->acronimo];
                          $f1 = $f1 != null ? $f1 : 0;
                          $f2 = $f2 != null ? $f2 : 0;
                          $total = $f1 + $f2;
                          $total = $total != 0 ? $total : '';                   
                          $data->put($key . '_' . $disciplina->acronimo,$total);                          

                       }

                        


                    }
                    
                    

                 }    
                      $total == '' ? '' : $total;
                      if($key == 'I TRIM NOTAS' || $key == 'II TRIM NOTAS'){
                        $total = $total / $disciplinas->count();
                      }
                      $data->put('total',round($total,1));
                      $avaliacao_aluno_collection->put($key,$data);
             }

                    $lista->push($avaliacao_aluno_collection);                    
          }        
                    $listaModelo = collect(["data"=>$lista]);                    
              
                    
        
        
                
        
       

       if(isset($director_turma)){
        $director_turma = $director_turma->nome;
       }else{
        $director = '';

       } 
        
       $user = auth()->user();

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
         
        
       .mytable{
         width:100%;
         border-collapse:collapse;
         font-size:11px;
         margin-top: -10px;
        }
        .cabecalho{
          margin-top:18.5px;
         
        }
        .cabecalho p{
          margin-top:-10px;
          text-transform:uppercase;
          font-size:10px;
        }
         .aluno_bio{
          margin-top:-10px;         
         }
         .aluno_bio span{
          margin-right: 24px;        
        }
        .mytable>th{
          text-align:center;
        }
        th,td{
          border:1px solid;       
        }
        .rodape{
          margin-top:20px;
        }
        .rodape .rodape-item{
          position:relative;
          float:left;
          font-size:10px;
          width:50%;
        }
        .rodape p{
          margin-top:-15px;          
        }

      </style>
      </head>
      <body onload='atribueCor()'>";
      $count = 0;
      foreach ($lista as $aluno){
        // dd($lista);
        $nome = $aluno['nome'];      
        $numero = $aluno['numero']; 
           
      $count++;
      if($count == 5){
        break;
      }       
          // $avaliacoes_do_aluno = $listaModelo->where('aluno_id',$aluno->id); 
        $output .="
        <div class='cabecalho' align='center' style='font-size: 12px;font-weight: bold;'' class='table-responsive text-uppercase'>
        <p>$instituicao->nome</p>                                        
        <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
        <p>PAUTA DE APROVEITAMENTO</p>            
        <p>FICHA DE NOTA DO $epoca->trimestre TRIMESTRE DO ANO LECTIVO $turma->ano_lectivo</p>    
       </div>
      <div class='aluno_bio' align='left' style='font-size: 12px;font-weight: bold;' class='text-uppercase'>
        <p><span>NOME: $nome</span>     <span>N: $numero</span>
           <span>CLASSE: $classe->nome</span>     <span>CURSO: $curso->acronimo</span>
        </p> 
      </div>      
      <div class='tabela_area'>
     <table class='mytable'>
     <thead border:1px solid;>
     <tr style='font-weight: bold;'>      
      <th scope='col' rowspan='1'style='width:15%;text-align:center'><span style='font-size:8;padding-left:5px;'>DISCIPLINAS</span></th>";
         
           foreach ($disciplinas as $disciplina) {
            $output .="
            <th scope='col' colspan='1'style='text-align:center'>$disciplina->acronimo</th>";            
           }
            $output .="        
            <th style='width:5%' scope='col' rowspan='1' class='centro'>MÉDIA</th>  
           </tr>     
          
           </thead>    
         <tbody>";

         foreach ($aluno as $key => $item) {

          if($key != 'nome' && $key != 'numero'){
             
          $output .="
         <tr>
           <td class='ct' style='text-align:center;font-weight:bold;'>
              <span>$key</span>
           </td>
           ";
            
            foreach($item as $i){

                $cor = 'black';
                if(is_null($i)){
                  $i = '';
                }else{
                  if($key != 'I TRIM FALTAS' 
                    && $key != 'II TRIM FALTAS'
                    && $key != 'TOTAL FALTAS'
                    && $key != 'CARGA HORÁRIA'){
                    
                        if($i != '' && $i < 10){
                          $cor = 'red';                       

                        }else if($i == '' &&  $i <= 0){
                          $i = '';                       

                        }
                        else{
                          $cor = 'black';
                          

                        }
                  }else{
                        if($i == '' || $i == 0){
                           $i = '';
                        }
                  }
                }        
                $output .="
                <td class='ct' style='text-align:center;font-weight:bold;'>
                    <span style='color:$cor;'>$i</span>
                </td>              
                ";         
             }
              $output .="
              
          </tr>";  
         }         
          
      }
        $output .="</tbody>
      </table>
      </div>
      <div class='rodape'  class='text-uppercase'>            
             <div class='rodape-item'>
               <p>OBS.: Faltas  (               ) </p>                    
               <p>Cabinda,      de      2019</p>
             </div>
             <div class='rodape-item' style='text-align:right;'>
               <p style='margin-bottom:-30px;font-size:10px;'>O(A) DIRECTOR(A) DE TURMA</p>                  
               <p>_____________________________</p>          
             </div>                    
      </div>
             ";   
            }
              $output .="
          </body>
      </html";

            return $output;
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
            $aluno = Aluno::find($data['aluno_id']);
            $turma = Turma::find($data['professor_id']);
            // dd($data);   
            $avaliacao = Avaliacao::create($data);
            // $aluno->avaliacaos()->associate($data['aluno_id'],$data);    
            // $turma = Turma::find($data['turma_id'])->disciplinas()->avaliacaos()->attach($data['professor_id'],$data);
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
    public function show($turmaId,$ProfessorId)
    {
       // return Modulo::find($moduloId,$ProfessorId);
        return view('admin.turmas.professors.index');
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
            $turma = Turma::find($data['turma_id'])->professores()->updateExistingPivot($data['Professor_id'],$request->only(['numero','cargo']));

            // $turma->attach($user);
        
   
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

       Turma::find($turmaId)->professores()->detach($professorId);
        return redirect()->back();
    }
}
