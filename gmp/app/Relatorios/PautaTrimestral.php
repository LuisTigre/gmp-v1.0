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
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use App\Relatorios\Minipauta;
use App\Relatorios\PautaTrimestral;
use App\Relatorios\PautaAnual;
use App\Relatorios\FichaDeAproveitamento;




class PautaTrimestral extends Controller
{
    public function pdf($turma_id){
      set_time_limit(120);
      return view('admin.relatorios.pautaTrimestral.index',compact('turma_id'));
      $viewhtml = View::make('admin.relatorios.pautaTrimestral.index',compact('turma_id'));
      $turma = Turma::find($turma_id);
      $epoca = Epoca::activo();
      $director_turma = $turma->professores()->where('director','s')->first();
      $pdf = new Dompdf;
      $pdf->loadHTML($viewhtml);      
      $pdf->set_paper('A3','landscape');
      $pdf->render();
      return $pdf->stream($turma->nome . ' Pauta '. $epoca->trimestre .' Trimestre.pdf',array('Attachment' => false));
      exit(0);
    }

    

}
