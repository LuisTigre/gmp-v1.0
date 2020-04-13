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
use App\Healper\Nota;
use PDF;
use App\Exports\PautaExport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use App\Events\AvaliacaoChanged;

        // dd($);
       $user = auth()->user();       
       $turma = Turma::find($turma_id);
       $sala = Sala::find($turma->sala_id);       
       // $disciplina = Disciplina::find($);
       $modulo = $turma->modulo()->first();       
       $curso = Curso::find($modulo->curso_id);
       $area = Area::find($curso->area_id);
       $classe = Classe::find($modulo->classe_id); 
       $coordenador = Professor::find($curso->professor_id);
       $director_instituicao = $curso->director_instituto_mae;

       $director_turma = $turma->professores()->where('director','s')->first(); 
       $epoca = Epoca::where('activo','S')->first();
       $instituicao = Instituicao::all()->first();  
       $epoca = Epoca::activo();

              
       $turma_info = $turma->nome_fragmentado();
      
       $listaModelo = Turma::avaliacao_trimestral_da_turma($turma_id,$epoca->trimestre);

       $cabecalhos = $turma->listaDisciplinas($turma->id,100);

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
   ?>
      <!DOCTYPE html>
      <html>
      <head>
      <title>PAUTA do {{$epoca->trimestre}} trimestre da turma {{$turma->nome}}</title> 
      <link href="{{ asset('css/relatorios.css') }}" rel="stylesheet">

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
                   height:70px;
                   width:1px;
                   word-break: keep-all;                   
           }         
           .centro {
                  text-align:center;
           }
           .just_left{
                  border-top: transparent;
                  border-bottom: transparent;
                  border-right: transparent;
           }
           .no_border{
                  border-top: transparent;
                  border-bottom: transparent;
                  border-right: transparent;
                  border-left: transparent;
           }
           .just_top{                  
                  border-bottom: transparent;
                  border-right: transparent;
                  border-left: transparent;
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
           #seccao_topo_grupo > #seccao_5,#seccao_topo_grupo > #seccao_6,#seccao_topo_grupo > 

           #seccao_7,#seccao_topo_grupo > #seccao_8{
                   float:left;
                   width:5%;
                   margin-top:-2px;
                   font-size:10px;
           }
         
           #seccao_topo_grupo > #seccao_5>p,
           #seccao_topo_grupo > #seccao_6>p,
           #seccao_topo_grupo > #seccao_7>p,
           #seccao_topo_grupo > #seccao_8>p{
                    margin-bottom:-12px;
           }

           #seccao_topo_grupo #seccao_1>p,
           #seccao_topo_grupo #seccao_5>p{
                    margin-bottom:-12px;
           }           
           
           #rodape{
                 text-transform:uppercase;
                 padding:-2px;
                 height:8%;                 
           }
           #rodape > div{
                    float:left;
                    width:25%;                    
           }
           #rodape div p{
                    padding-top: -10px;
           }
           .linha{
                    height:10px;
           }
           .obs{
                    font-size:7px;
                    padding-left:15px;
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
      <body>
      	<div>
          <div id='cabecalho' align='center' style='font-size: 12px;
              font-weight: bold;' class='table-responsive text-uppercase'>

              <p>{{$instituicao->nome}}</p>                           
              <p>{{$instituicao->lema}}</p>                                       
              <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
              <p>PAUTA DE APROVEITAMENTO</p>            
              <p>ÁREA DE FORMAÇÃO: {{$area->nome}}</p>           
              <p>REGIME DIURNO (<span>{{$turma->periodo}}</span>)</p>
          </div>

          <div id='seccao_topo_grupo' align='center' style='font-size: 12px;'>
              <div id='seccao_1'>
                   <p>A DIRECTOR(A) DO INSTITUTO </p> 
                   <p>_________________________________</p> 
                   <p class='text-uppercase'>{{$director_instituicao}}</p>
                   <p>DATA {{date('d')}} / {{ date('m')}} / {{date('Y')}} </p> 
              </div>

              <div id='seccao_2'>
                  <p>ANO LECTIVO {{$turma->ano_lectivo}}</p> 
                  <p>{{$classe->nome}} CLASSE TURMA: {{$turma_info[2]}}</p>         
              </div>

              <div id='seccao_3'>
                  <p>CURSO: {{$curso->nome}}</p>
              </div>

              <div id='seccao_4'>
                  <p>{{$epoca->trimestre}}  TRIMESTRE</p>
              </div>

              <div id='seccao_5' align='left'>
                  <p>TOTAL: {{$totalAlunos}}</p>
                  <p>M: {{$alunos_m}}</p>
                  <p>F: {{$alunos_f}}</p>
                  <p style='text-transform:uppercase;'>{{$sala->nome}}</p>
              </div>
          </div>         
          <div id='tabela_area'>
              <table id='mytable' style="width:100%">
                <thead border:1px solid;>
                   <tr style='font-weight: bold;'>

                      <th class='centro' scope='col' rowspan='3'>Nº</th>

                      <th scope='col' rowspan='3'style='width:5%;'>
                          <div style='font-size:10px;text-align:center;'>Nº_PROC</div>
                      </th>

                      <th scope='col' rowspan='3'style='width:15%;'>
                          <span style='font-size:10;padding-left:5px;'>NOME</span>
                      </th>

                      <th class='centro' scope='col' style='width:1%;font-size:10px;' rowspan='3'>
                          <p class='vtexto'>IDADE</p>
                      </th>


                      @foreach($cabecalhos as $key => $value)
                         
                          <th scope='col' colspan='3'class='centro'>{{$value->acronimo}}</th>
                      
                      @endforeach
                     

                      <th style='width:1%' scope='col' rowspan='3' class='centro'>
                          <div class='vtexto'>GÊNERO</div>
                      </th>

                      <th style='width:0.1%' scope='col' rowspan='3' class='centro'>
                          <div class='vtexto'>MÉDIA</div>
                      </th>

                      <th style='width:5%' scope='col' rowspan='3' class='centro'>OBS</th>  
                      <th style='width:1%' scope='col' colspan='2' rowspan='3' class='centro just_left'></th>  
                  </tr>
                  <tr>
                          <?php 
	                      $trim = $epoca->trimestre;
	                      $ct = $trim == 'I'? 'CT1' : ($trim == 'II'? 'CT2':'CT3');?>

                      @foreach($cabecalhos as $key => $value)
                            
                            <th style='font-size:8px;' scope='col' colspan='2' class='centro'>FALTAS</th>  
                            <th scope='col' rowspan='2' class='centro'>{{$ct}}</th>
                            
                      @endforeach
                             
                  </tr>
                  <tr>
                      @foreach($cabecalhos as $key => $value)
                          
                            <th scope='col' class='centro'>J</th>  
                            <th scope='col' class='centro'>I</th>    
                      @endforeach                  
                      

                  </tr>
                </thead>
                <tbody>
                      @foreach($listaModelo['data'] as $key=>$aluno)
                          <?php      
                          $count = 0;    
                          $fundo = '';      
                          $vermelhado = '';
                          $status = $aluno['OBS2'];

                          if($status == 'Desistido' || $status == 'Suspenso' || $status == 'Transferido'){
                              
                              $status=='Desistido'? $fundo = 'yellow':($status=='Suspenso'? 
                              $fundo = 'rgb(255,212,227)' : $fundo ='rgb(247,221,252)');
                                
                          }
                          $ct = 7;
                          ?>
                          <tr style='font-size:8px;background:{{$fundo}};'>
                            @foreach($aluno as $chave => $value) 
                                <?php                                      
                                $count++;
                                $cor = 'black';        
                                if($chave == 'OBS2'){
                                  $result = explode(' ', $value);
                                   if(isset($result[1])){
                                      $def = $result[0];
                                      $obsr = $result[1];
                                      $cor = $obsr == 'Transita'? 'black':'red';                          
                                   }else{
                                      $cor = 'red';
                                      $def = ' ';
                                      $obsr = $value;
                                   }
                                   ?>
                                    <td class='just_left' style='font-weight:bold;text-align:right;background:white;'>
                                        <span class='obs' style='color:{{$cor}};margin-right:1px;'>{{$def}}</span>                                      
                                    </td>
                                    <td class='no_border' style='font-weight:bold;background:white;'>
                                        <span style='color:{{$cor}};text-align:left;'>{{$obsr}}</span> 
                                    </td>                       
                                <?php
                                }else if($count == $ct || $chave == 'Media'){                                    
                                    if($value != '' && floatval($value) < 10){
                                          $cor = 'red'; 

                                    }
                                  ?>  
                                    <td class='ct' style='text-align:center;font-weight:bold;'>
                                        <span style='color:{{$cor}};text-transform:uppercase;font-size:8px;'>{{$value}}</span>
                                    </td>       
                                 <?php  
                                    $ct +=3;

                                  }else if($count==sizeof($aluno)-1){
                                        floatval($value) <= 10 ? $cor = 'red':$cor = 'black';
                                 
                                    /*valores da média*/
                                  ?>
                                        <td style='text-align:center;font-weight:bold;'>
                                            <span style='color:{{$cor}};'>{{$value}}</span>
                                        </td><?php

                                  }else if($count==sizeof($aluno)-2 || $count==1 || $count==4){ ?>
                                        <td class='centro'>{{$value}}</td><?php                               

                                  }else if($count==3 || $count==2){?>                                     
                                        <td>{{$value}}</td><?php

                                  }else{?>                                        
                                        <td class='centro'>{{$value}}</td><?php
                                  }
                                  ?>
                            @endforeach 
                          </tr>
                        @endforeach 

                          <tr>
                              <td colspan='4'>
                                  <p style='text-align:center;margin:1px;'>
                                    DATA DO CONSELHO DA TURMA {{date('d')}} / {{ date('m')}} / {{date('Y')}} 
                                  </p> 
                              </td>

                              @foreach ($cabecalhos as $key => $value) 
                                 <td colspan='3'></td>
                              @endforeach

                              <td colspan='3'></td>
                              <td class='just_left' colspan='1'></td>
                          </tr>
	                      <?php                         
	                      $colspan = intVal(7+sizeof($cabecalhos)*3);                        
	              		  ?>
                          <tr>
                              <td class='just_top' colspan='{{$colspan}}'>
                                  <div id='rodape' align='center'>

                                        <div id='seccao_1'>
                                           <p>O(A) DIRECTOR(A) DE TURMA                  
                                           <p class='linha'>_________________________________</p>

                                           <p margin-top:5px;>{{$director_turma}}</p> 
                                        </div>

                                        <div id='seccao_2'>
                                           <p>O COORDENADOR DO CURSO</p> 
                                           <p class='linha'>_________________________________</p> 
                                           <p>{{$coordenador->nome}}</p>         
                                        </div>

                                        <div id='seccao_3'>
                                           <p>O SUB-DIRECTOR PEDAGÓGICO</p>
                                           <p class='linha'>_________________________________</p>  
                                           <p>{{$instituicao->director_pedagogico}}</p>
                                        </div>

                                        <div id='seccao_4'>
                                           <p> O DIRECTOR DO COLÉGIO</p>
                                           <p class='linha'>_________________________________</p>  
                                           <p>{{$instituicao->director_instituicao}}</p>              
                                        </div>                                                  
                                  </div>                          
                              </td>                              
                              <td class='no_border' colspan='1'></td>
                          </tr>
                          @foreach ($listaModelo['estatistica'] as $estatistica => $valores) <?php
                          
                                  $perc = $estatistica == '%Reprovados' ? '%' : ($estatistica == '%Aprovados' ? '%' : '');                         
                          ?>
                              <tr >
                                  <td class='no_border' colspan='2'></td>
                                  <td class='no_border' colspan='2' style='text-align:right;'>{{$estatistica}}</td>
                                  

                                  @foreach ($valores as $key => $value) 
                                     <td class='no_border' colspan='1' style='text-align:right;'></td>
                                     <td class='no_border' colspan='2' style='text-align:right;'>
                                        <span style='margin-right:10%;font-weight:bold;font-size:10px;' >{{$value}}{{$perc}}</span>
                                     </td>
                                   @endforeach

                                  <td class='no_border' colspan='3'></td>
                                  <td style='text-align:right;font-size:10px;font-weight:bold;' class='no_border' colspan='1'>
                                      <span style='margin-right:1px;'>{{$listaModelo[$estatistica]}}{{$perc}}</span>
                                  </td>
                              </tr>                        
              
                          @endforeach

                </tbody>
              </table>
          </div>
        <div> 
      </body>
      </html">