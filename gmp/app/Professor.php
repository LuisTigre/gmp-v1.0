<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Professor extends Model
{
   use SoftDeletes;
              
   protected $fillable = ['nome','telefone','email'];
   
   protected $dates = ['deleted_at'];

   public function user()
   {
    return $this->belongsTo('App\user');
   }
   public function curso()
   {
   	return $this->belongsTo('App\curso');
   }

   public function turmas()
   {
    return $this->belongsToMany('App\turma','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }

   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina','disciplina_turma')->withPivot('director','disciplina_id','professor_id')->withTimestamps();
   }



   public static function listaProfessores($paginate)
   {
    
       $user = auth()->user();
       if($user->admin == "S"){
           $listaProfessores = DB::table('professors')                       
                       ->select('professors.id','professors.nome',
                        'professors.telefone','professors.email')
                       ->whereNull('deleted_at')
                       ->orderBy('professors.id','DESC')
                       ->paginate($paginate);
       }else{

       $listaProfessores = DB::table('professors')
                       ->select('professors.id','professors.nome',
                        'professors.telefone','professors.email')
                       ->whereNull('deleted_at')
                       ->where('professors.email','=',$user->email)
                       ->orderBy('professors.id','DESC')
                       ->paginate($paginate);
       }       
       return $listaProfessores;
   }
    
    public function estatistica($epoca_id){

       $user = auth()->user();       
       $epoca = Epoca::find($epoca_id);
       $ano_lectivo = $epoca->ano_lectivo;       
       $trim = $epoca->trimestre;       
       $cursos = collect([]);
       $coordenadores = collect([]);
       $output = collect([]);
       $todos_alunos = 0;
       $rep_total= 0;
       $apr_total= 0;
      

       $turmas = $this->turmas()->where('ano_lectivo',$ano_lectivo)->get();
       $listaCabecalho = ['ALUNOS EXISTENTES','MÉDIA DE NOTAS','Nº DE POSETIVAS','Nº DE NEGATIVAS','NOTA MAIS ALTA','NOTA MAIS BAIXA','DESISTÊNCIAS','ALUNOS NÃO AVALIADOS','REPROVADOS','APROVADOS'];
       
        foreach ($listaCabecalho as $key => $cabecalho) {
            $total = 0;            
            $lista = collect([]);
            $lista->put('categoria',$cabecalho);            
            $data2 = collect([]);            
            $nao_avaliadoss = collect([]);
            $nota_mais_baixa_anterior = 20;
            foreach ($turmas as $chave => $turma) {           
                $data = collect([]);
                $dados2 = collect([]);
                $disciplina = Disciplina::find($turma->pivot->disciplina_id);                     
                $modulo = Modulo::find($turma->modulo_id);       
                $curso = Curso::find($modulo->curso_id);
                $classe = Classe::find($modulo->classe_id);
                $notas = collect([]);

                $dados2->put($curso->id,$curso->nome);             
                $dados2->put($curso->acronimo,$curso->acronimo);

                $coordenador = Professor::find($curso->professor_id);             
                $dados2->put($coordenador->id,$coordenador->nome); 

                $director_turma = $turma->professores()->where('director','s')->first();
                $director_turma = $turma->nome != null ? $turma->nome : '';
                $alunosDaDisciplina = $disciplina->buscarAlunosDaDisciplina($turma);  
                $alunosDaTurma = Turma::listaAlunos($turma->id,100);
                $listaModelo = Turma::avaliacaoTrimestralDaTurmaParaProfessor($turma,$trim)->where('disciplina_id',$disciplina->id);              
                $nota = 0;              
                foreach ($listaModelo as $item) {                  
                     $aluno_id = $item->aluno_id;
                     $notas->push($item->ct);
                     $aluno_da_turma = $alunosDaTurma->where('id',$aluno_id)->first();          
                    if($alunosDaDisciplina['data']->where('id',$aluno_id)->isNotEmpty() && $aluno_da_turma->status == 'Activo'){ 
                      $data->push($item);
                    }
                }
                $listaModelo = collect([]);
                $listaModelo->put('data',$data);              
                $listaCabecalho2 = $turma->acronimo;
                $listadisciplinas = Disciplina::orderBy('nome')->get();       
                $listaProfessores = Professor::orderBy('nome')->get();
                $totalAlunos = sizeof($alunosDaDisciplina['data']);              
                $aprovados = $listaModelo['data']->where('ct','>=',10);
                $reprovados = $listaModelo['data']->where('ct','<',10);                
                $desistencias = $alunosDaTurma->where('status','Desistido')->count();
                
                $nao_avaliados = $listaModelo['data']->where('mac',null)->where('p1',null)->count();
                foreach ($alunosDaTurma as $aluno_da_turma) {
                  $existe = $listaModelo['data']->where('aluno_id',$aluno_da_turma->id)->isEmpty();
                  if($existe){
                     $nao_avaliados++;
                     $nao_avaliadoss->push($aluno_da_turma);

                  }
                }


               /*QUANTIDADES*/
               
                $media = $notas->median();
                $nota_mais_alta = $notas->max();
                $nota_mais_baixa = $notas->min();
                $aprovados_qtd = $aprovados->count();                          
                $reprovados_qtd = $reprovados->count();      
                
                if($key == 0){
                    $lista->put('existentes_' . $turma->nome . '_'. $chave,$alunosDaTurma->count());
                    $total += $alunosDaTurma->count();
                    $todos_alunos += $alunosDaTurma->count();;
                }else if($key == 1){
                   
                    $lista->put('medias' . $turma->nome . '_'. $chave,$media);
                    $total += $media/$turmas->count();              
                }else if($key == 2){
                    $lista->put('posetivas_' .  $turma->nome . '_'. $chave,$aprovados_qtd);
                    $total += $aprovados_qtd;
                }else if($key == 3){
                    $lista->put('negativas_' .  $turma->nome . '_'. $chave,$reprovados_qtd);
                    $total += $reprovados_qtd;
                }else if($key == 4){
                    $lista->put('nota_mais_altas_' .  $turma->nome . '_'. $chave,$nota_mais_alta);
                    $total = $nota_mais_alta > $total ? $nota_mais_alta : $total;              
                }else if($key == 5){
                    $lista->put('nota_mais_baixa_' .  $turma->nome . '_'. $chave,$nota_mais_baixa);                 
                    $total = $nota_mais_baixa < $nota_mais_baixa_anterior ? $nota_mais_baixa : $nota_mais_baixa_anterior;
                }else if($key == 6){
                   $lista->put('desistencias_' . $turma->nome . '_'. $chave,round($desistencias));
                     $lista->put('desistencias_' . $turma->nome . '_'. $chave,round(($desistencias*100)/$alunosDaTurma->count(),1) . '%');
                     $total += $desistencias;
                                  
                }if($key == 7){
                    $lista->put('nao_avaliados_' . $turma->nome . '_'. $chave,round(($nao_avaliados*100)/$alunosDaTurma->count(),1) . '%');
                    $total += $nao_avaliados;              
                }else if($key == 8){                
                    $rep = ($reprovados_qtd*100)/$alunosDaTurma->count();
                    $lista->put('reprovados_' .  $turma->nome . '_'. $chave,round($rep) . '%');                  
                    $rep_total += $reprovados_qtd;

                }else if($key == 9){                
                    $ap = ($aprovados_qtd*100)/$alunosDaTurma->count();
                    $lista->put('aprovados_' .  $turma->nome . '_'. $chave,round($ap) . '%');                 
                    $apr_total += $aprovados_qtd;                  
                }
                $nota_mais_baixa_anterior = $nota_mais_baixa;
            }
                
                // dd($nao_avaliadoss);

                if($key == 6){
                  $total = ($total*100)/$todos_alunos;
                }
                if($key == 7){
                  $total = ($nao_avaliadoss->count()*100)/$todos_alunos;
                }
                if($key == 8){
                  $total = ($rep_total*100)/$todos_alunos;
                }
                if($key == 9){              
                  $total = ($apr_total*100)/$todos_alunos;              
                }
                if($key >= 6){
                  $lista->put('total_' . $cabecalho,round($total,1) . '%');

                }else{
                  $lista->put('total_' . $cabecalho,round($total,1));
                }

            $output->push($lista);
            $data2->put($curso->id,$dados2);
        }
        $dados = collect([]);
        $dados->put('data',$output);
        $dados->put('data2',$data2);       
        $dados->put('professor',$this);

        return($dados);
      }

    public function listaTurmas($paginate){
      $epoca = Epoca::where('Activo','S')->get()->first();
      $turmas = $this->turmas()->get()->where('ano_lectivo',$epoca->ano_lectivo);

         $user = auth()->user();
         $output =collect([]);

       if($user->professor == "S" || $user->admin == "S"){

           $output = DB::table('disciplina_turma')                       
                       ->join('turmas','turmas.id','=','disciplina_turma.turma_id')
                       ->join('professors','professors.id','=','disciplina_turma.professor_id')
                       ->join('disciplinas','disciplinas.id','=','disciplina_turma.disciplina_id')
                       ->where('disciplina_turma.professor_id',$this->id)
                       ->select('turmas.id','turmas.nome as turma','disciplinas.nome as disciplina'
                        ,'turmas.ano_lectivo','disciplina_turma.director')                      
                       ->orderBy('turmas.ano_lectivo','DESC')
                       ->paginate($paginate);
       }

       // dd($output);     
       return $output;

    }
   
}
