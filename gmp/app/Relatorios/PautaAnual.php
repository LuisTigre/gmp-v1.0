<?php

namespace App\Relatorios;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Modulo;
use App\Disciplina;
use App\Classe;
use App\Curso;
use App\Professor;
use App\Turma;
use App\Aluno;
use App\Avaliacao;
use App\Epoca;
use App\Sala;
use App\Instituicao;
use App\Area;
use PDF;
use App\Exports\PautaExport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;



class PautaAnual
{
    function pdf($turma_id){
      set_time_limit(120);
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_html($turma_id));      
      $pdf->set_paper('A3','landscape');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }    
    function convert_html($turma_id){
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
          <th scope='col' colspan='1' rowspan='3' class='centro' style='font-size:5px'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>$value->acronimo</p></th>";    
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
          }
          if($qtd == 2){
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
          $status=='Desistido'? $fundo = 'yellow':($status=='Suspenso'? $fundo = 'rgb(255,212,227)' : $fundo ='rgb(247,221,252)');
            
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


}
