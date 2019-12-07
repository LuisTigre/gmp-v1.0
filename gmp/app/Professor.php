<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Epoca;


class Professor extends Model
{
   use SoftDeletes;
              
   protected $fillable = ['nome','telefone','email'];
   
   protected $dates = ['deleted_at'];

   public function user()
   {
    return $this->belongsTo('App\User');
   }
   public function curso()
   {
   	return $this->belongsTo('App\Curso');
   }

   public function turmas()
   {
    return $this->belongsToMany('App\Turma','disciplina_turma')->withPivot('director','disciplina_id')->withTimestamps();
   }

   public function disciplinas()
   {
    return $this->belongsToMany('App\disciplina','disciplina_turma')->withPivot('director','disciplina_id','professor_id')->withTimestamps();
   }



   public static function listaProfessores($paginate)
   {
       set_time_limit(60*30);       
       $epoca = Epoca::where('Activo','S')->first();
                     
       $user = auth()->user();
       if($user->admin == "S"){
           $listaProfessores = DB::table('professors')                       
                       ->select('professors.id','professors.nome',
                        'professors.telefone','professors.email',
                        'professors.email as avaliados')
                       ->whereNull('deleted_at')
                       ->orderBy('professors.id','DESC')
                       ->paginate($paginate);
                        // Cache::flush();
           foreach ($listaProfessores as $prof) {
              $teacher = Professor::find($prof->id);
              $turmas = $teacher->turmas()->get();
              if($turmas->isNotEmpty()){                
                   if (Cache::has($teacher->id . '_percentagem_de_nao_avaliados')){          
                      $prof->avaliados = Cache::get($teacher->id . '_percentagem_de_nao_avaliados');
                   }else{
                      $prof->avaliados = 'N/D';
           //            $estatistica = $teacher->estatistica($epoca->id);                      
           //            $nao_avaliados = $estatistica['data'][6]['total_NOTAS EM FALTA'];                      
           //            $prof->avaliados = $estatistica['data'][6]['total_NOTAS EM FALTA'];
                      
           //            cache([$teacher->id . '_percentagem_de_nao_avaliados' => $prof->avaliados], now()->addSeconds(60*60*5));
                   }
              }else{
                 $prof->avaliados = '-';

              }
           }
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
       $apr_total_pertual = 0;
       $notas_alta_baixa = collect([]);
       $result;
      

       $turmas = $this->turmas()->where('ano_lectivo',$ano_lectivo)->get();
       $listaCabecalho = ['ALUNOS EXISTENTES','MÉDIA DE NOTAS','Nº DE POSETIVAS','Nº DE NEGATIVAS','NOTA MAIS ALTA','NOTA MAIS BAIXA','NOTAS EM FALTA','REPROVADOS','APROVADOS'];
       
        foreach ($listaCabecalho as $key => $cabecalho) {
            $total = 0;            
            $lista = collect([]);
            $lista->put('categoria',$cabecalho);            
            $data2 = collect([]);            
            $nao_avaliadoss = collect([]);
            
            foreach ($turmas as $chave => $turma) {           
                $data = collect([]);
                $dados2 = collect([]);
                $disciplina = Disciplina::find($turma->pivot->disciplina_id); 
                $estatistica = $disciplina->estatistica($turma->id)[$trim]; 

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

                $aprovados = $estatistica['aprovados'];
                $reprovados = $estatistica['reprovados'];     
                $alunos_existentes = $estatistica['alunos_existentes'];
                $media = $estatistica['media'];
                $nota_mais_alta = is_null($estatistica['nota_mais_alta']) ? 0 : $estatistica['nota_mais_alta']->ct;      
                $nota_mais_baixa = is_null($estatistica['nota_mais_baixa']) ? 0 : $estatistica['nota_mais_baixa']->ct;
                $numero_de_posetivas = $estatistica['numero_de_posetivas'];                          
                $numero_de_negativas = $estatistica['numero_de_negativas'];       
                $notas_em_falta = $estatistica['notas_em_falta'];       
                
                if($key == 0){
                    $lista->put('existentes_' . $turma->nome . '_'. $chave,$alunos_existentes);
                    $total += $alunos_existentes;
                    $todos_alunos += $alunos_existentes;;
                }else if($key == 1){
                   
                    $lista->put('medias_' . $turma->nome . '_'. $chave,$media);
                    $total += $media;              
                }else if($key == 2){
                    $lista->put('posetivas_' .  $turma->nome . '_'. $chave,$numero_de_posetivas);
                    $total += $numero_de_posetivas;
                }else if($key == 3){
                    $lista->put('negativas_' .  $turma->nome . '_'. $chave,$numero_de_negativas);
                    $total += $numero_de_negativas;
                }else if($key == 4){
                    $lista->put('nota_mais_alta_' .  $turma->nome . '_'. $chave,$nota_mais_alta);
                    if($nota_mais_baixa){
                        $notas_alta_baixa->push($nota_mais_alta);                        
                    }          
                }else if($key == 5){
                    $lista->put('nota_mais_baixa_' .  $turma->nome . '_'. $chave,$nota_mais_baixa);   
                    if($nota_mais_baixa){
                        $notas_alta_baixa->push($nota_mais_baixa);                        
                    }              
                }else if($key == 6  ){
                    $lista->put('nao_avaliados_' . $turma->nome . '_'. $chave,round($notas_em_falta,1));
                    $total += $notas_em_falta;              
                }else if($key == 7){              
                    $lista->put('reprovados_' .  $turma->nome . '_'. $chave,round($reprovados) . '%');    
                    $rep_total += $reprovados;

                }else if($key == 8){                
                    $lista->put('aprovados_' .  $turma->nome . '_'. $chave,round($aprovados) . '%');     
                    $apr_total += $aprovados ;                
                }
                
            }
                
                

                if($key == 1){
                  $total = $total/$turmas->count();
                }
                if($key == 4){
                  $total = $notas_alta_baixa->max();
                }
                if($key == 5){
                  $total = $notas_alta_baixa->min();                       
                }
                if($key == 7){
                  $total = $rep_total/$turmas->count();                                  
                }
                if($key == 8){              
                  $total = $apr_total/$turmas->count();
                  $apr_total_pertual = $total;             
                }
                if($key >= 7){
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

        if($apr_total_pertual >= 90){
            $result = 'EXCELENTE';
        }else if($apr_total_pertual >= 70){
            $result = 'BOM';

        }else if($apr_total_pertual >= 50){
            $result = 'RAZOÁVEL';

        }else{
            $result = 'MAU';

        }
        $dados->put('result',$result); 
        // dd($dados);
        return $dados;
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
