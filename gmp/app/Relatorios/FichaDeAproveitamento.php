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



class FichaDeAproveitamento
{

    function pdf($turma_id){
      set_time_limit(60*60);
      $turma = Turma::find($turma_id);
      $director_turma = $turma->professores()->where('director','s')->first();
      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_html($turma_id));      
      $pdf->set_paper('A4','portrait');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
    }


    function convert_html($turma_id){
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
            $aluno_total_faltas = 0;
            $avaliacao_aluno_collection = collect([]);
            $aluno_avaliacao = $listaModelo->where('aluno_id',$aluno->id);              
            $avaliacao_aluno_collection->put('nome',$aluno->nome);
            $avaliacao_aluno_collection->put('numero',$aluno->numero);
            $avaliacao_aluno_collection->put('devedor',$aluno->devedor);
            
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
                          $element = $aluno_avaliacao_aluno_disc->fnj2;
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;

                       }else if($key == 'TOTAL FALTAS'){
                          $f1 = $avaliacao_aluno_collection['I TRIM FALTAS']['I TRIM FALTAS_' . $disciplina->acronimo];
                          $f2 = $avaliacao_aluno_collection['II TRIM FALTAS']['II TRIM FALTAS_' . $disciplina->acronimo];
                          $f1 = $f1 != null ? $f1 : 0;
                          $f2 = $f2 != null ? $f2 : 0;
                          $total = $f1 + $f2;
                          $aluno_total_faltas += $total;                          
                          $total = $total != 0 ? $total : '';                   
                          $data->put($key . '_' . $disciplina->acronimo,$total);

                       }else if($key == 'CARGA HORÁRIA'){
                          $element = $disciplina->pivot->carga;                                             
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          $total += $element;                         
                          

                       }else if($key == 'ESTADO'){
                          $total_faltas = $avaliacao_aluno_collection['TOTAL FALTAS']['TOTAL FALTAS_' . $disciplina->acronimo];
                          $total_faltas = $total_faltas != null ? $total_faltas : 0;
                          $carga = $disciplina->pivot->carga;
                          $estado = $total_faltas >= $carga * 3 ? 'EXC.' : 'MANT.';
                          $data->put($key . '_' . $disciplina->acronimo,$estado);                        

                       }
                                                     
                    }else{
                      if($key == 'CARGA HORÁRIA'){
                          $element = $disciplina->pivot->carga;
                          $total += $element;                   
                          $data->put($key . '_' . $disciplina->acronimo,$element);
                          
                       }else{
                          $data->put($key . '_' . $disciplina->acronimo,'');

                       }

                       //  if($key == 'I TRIM FALTAS'){
                       //    $element = 10;
                       //    $data->put($key . '_' . $disciplina->acronimo,$element);
                       //    $total += $element;
                       //  }
                       //  if($key == 'II TRIM NOTAS'){
                       //    $element = 10;
                       //    $data->put($key . '_' . $disciplina->acronimo,$element);
                       //    $total += $element;
                       //  }
                       //  if($key == 'II TRIM FALTAS'){
                       //    $element = 10;
                       //    $data->put($key . '_' . $disciplina->acronimo,$element);
                       //    $total += $element;
                       //  }

                       //  if($key == 'TOTAL FALTAS'){
                       //    $f1 = $avaliacao_aluno_collection['I TRIM FALTAS']['I TRIM FALTAS_' . $disciplina->acronimo];
                       //    $f2 = $avaliacao_aluno_collection['II TRIM FALTAS']['II TRIM FALTAS_' . $disciplina->acronimo];
                       //    $f1 = $f1 != null ? $f1 : 0;
                       //    $f2 = $f2 != null ? $f2 : 0;
                       //    $aluno_total_faltas += $total;
                       //    $total = $f1 + $f2;
                       //    $total = $total != 0 ? $total : '';                   
                       //    $data->put($key . '_' . $disciplina->acronimo,$total);                          

                       // }

                        


                    }
                    
                    

                 }    
                      $total == '' ? '' : $total;
                      if($key == 'I TRIM NOTAS' || $key == 'II TRIM NOTAS'){
                        $total = $total / $disciplinas->count();
                      }
                      if($key == 'TOTAL FALTAS'){
                        $total = $aluno_total_faltas;
                        $avaliacao_aluno_collection->put('ALUNO_TOTAL_FALTAS',$total);
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
        body{
          margin: 0px;
          padding: 0px;
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
        $aluno_total_faltas = $aluno['ALUNO_TOTAL_FALTAS'];
           
      $count++;
      if($count == 33){
        // continue;
      }
      if($aluno['devedor'] == 'S'){
         continue;
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
        <p><span>NOME: $nome</span>     <span>Nº: $numero</span>
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

          if($key != 'nome' && $key != 'numero' && $key != 'ALUNO_TOTAL_FALTAS' && $key != 'devedor'){
             
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
                // }else if($key == 'ESTADO'){
                //   $cor = 'black'; 

                }else{
                  if($key != 'I TRIM FALTAS' 
                    && $key != 'II TRIM FALTAS'
                    && $key != 'TOTAL FALTAS'
                    && $key != 'CARGA HORÁRIA'){
                    
                        if(($i != '' && $i < 10) && ($i != 'MANT.' && $i != 'EXC.')){
                          $cor = 'red';                       

                        }else if($i == '' &&  $i <= 0){
                          $i = '';                       

                        }else{
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
               <p>OBS.: Faltas  ( $aluno_total_faltas ) </p>                    
               <p>Cabinda,  " . date('d').' / '.date('m').' / '.date('Y') . "</p>               
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



}
