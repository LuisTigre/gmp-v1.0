<?php

namespace App\Http\Controllers\Admin;

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
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use App\Relatorios\Minipauta;
use App\Relatorios\PautaTrimestral;
use App\Relatorios\PautaAnual;
use App\Relatorios\FichaDeAproveitamento;


class RelatoriosController extends Controller
{
   function minipauta($turma_id,$disciplina_id){
       $relatorio = new Minipauta();
       $relatorio->pdf($turma_id,$disciplina_id);      
    }

   function pautaTrimestral($turma_id){
         // return view('admin.relatorios.pautaTrimestral.index',compact('turma_id'));
         $turma = Turma::find($turma_id);
         $epoca = Epoca::activo();
         $viewhtml = View::make('admin.relatorios.pautaTrimestral.index',compact('turma_id'));
         $paper_format ='A3';
         $orientation ='landscape';
         $doc_output_name = $turma->nome . ' Pauta '. $epoca->trimestre .' Trimestre.pdf';
         $this->lunchPdf($viewhtml,$paper_format,$orientation,$doc_output_name);
           
    }
   function pautaAnual($turma_id){
       $relatorio = new PautaAnual();
       $relatorio->pdf($turma_id);      
    }
    function pautaAnualhtml($turma_id){
             
         set_time_limit(1000);
         return view('admin.relatorios.pautaAnual.index',compact('turma_id'));
         $turma = Turma::find($turma_id);
         $epoca = Epoca::activo();

         $viewhtml = View::make('admin.relatorios.pautaAnual.index',compact('turma_id'));
         $paper_format ='A3';
         $orientation ='landscape';
         $doc_output_name = $turma->nome . ' Pauta Anual'. $epoca->trimestre;
         $this->lunchPdf($viewhtml,$paper_format,$orientation,$doc_output_name);
         
    }
   function ficha_de_aproveitamento($turma_id){
       $relatorio = new FichaDeAproveitamento();
       $relatorio->pdf($turma_id);      
    }

    public function lunchPdf($viewhtml,$paper_format,$orientation,$doc_output_name){
           $pdf = new Dompdf;
           $pdf->loadHTML($viewhtml);      
           $pdf->set_paper($paper_format,$orientation);
           $pdf->render();
           return $pdf->stream($doc_output_name,array('Attachment' => false));
           exit(0);
    }


}
