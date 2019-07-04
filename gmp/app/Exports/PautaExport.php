<?php 
namespace App\Exports;

use App\User;
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
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PautaExport implements FromView
{
    public function view(): View
    {

       $user = auth()->user();       
       $turma = Turma::find(9);       
       $turma_id = $turma->id;       
       // $disciplina = Disciplina::find($);
       $sala = Sala::find($turma->sala_id);       
       $modulo = $turma->modulo()->first();
       $curso = Curso::find($modulo->curso_id);
       $classe = Classe::find($modulo->classe_id); 
       $area_formacao = Area::find($curso->area_id); 
       $coordenador = Professor::find($curso->professor_id);  
       $director_turma = $turma->professores()->where('director','s')->first();
       $status = ''; 
       $epoca = Epoca::where('activo','S')->first();
       $instituicao = Instituicao::all()->first();       
       $director_instituicao = $curso->director_instituto_mae;

       if(is_null($director_instituicao)){
       $director_instituicao = $instituicao->director_instituicao;
       }                
              
       $turma_info = explode(' ', $turma->nome);           
     
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
       
       
       $disciplinas_terminais = $modulo->disciplinas()->where('terminal','S')->where('curricular','S')->get();
       if(isset($director_turma)){
        $director_turma = $director_turma->nome;
       }else{
        $director = '';

       } 

       $user = auth()->user();       

     return view('admin.turmas.pautaEXCEL.index',compact('turma','disciplina','classe','disciplinas_terminais','disc_terminadas_10','disc_terminadas_11','disc_terminadas_12','disc_terminal','disc_anterior','listaCabecalho','listaCabecalho2','listaModelo','status '));


        // return view('admin.turmas.pautaexcel.index', [
        //     'users' => User::all()
        // ]);
    }
}