<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\AlunosExport;
use App\Imports\AlunosImport;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Dompdf\Dompdf;
use App\Aluno;
use App\Modulo;
use App\Turma;
use App\Disciplina;
use App\Curso;
use App\Classe;
use App\Epoca;
use App\Avaliacao;
use App\Area;
use App\Instituicao;
// use Excel;


class AlunosController extends Controller
{    

    public function index()
    {
    $listaMigalhas = json_encode([
        ["titulo"=>"Admin","url"=>route('admin')],
        ["titulo"=>"Lista de Alunos","url"=>""]
    ]);
       
       $listaModelo = Aluno::listaAlunos(100);       
       $listaModulos = Modulo::all();

       return view('admin.alunos.index',compact('listaMigalhas','listaModelo','listaModulos'));

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
        $data = $request->all();
        if($request->has('nome')){
        $validacao = \Validator::make($data,[
        'nome' => 'required|string|max:255',
        'idmatricula' => 'required|string|max:11',
        'data_de_nascimento' => 'required|string|max:255',
        'telefone' => 'required|string|max:9|min:9',
        'encarregado_tel' => 'required|string|max:9|min:9',       
        'email' => 'required|string|unique:alunos',       
        'modulo_id' => 'required',       
        'doc_local_emissao' => 'required',       
        'doc_data_emissao' => 'required',       
        'doc_data_validade' => 'required',       
        'provincia' => 'required',       
        'pais' => 'required',       
        'morada' => 'required',       
        'escola_origem' => 'required'      
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        $user = auth()->user();        
        // $user->alunos()->create($data);
        $aluno = Aluno::create($data);
        $aluno->user()->associate($user);
        $aluno->save();        
        return redirect()->back();
                   

    }else if($request->has('excel_file')){
      Excel::import(new AlunosImport,request()->file('excel_file'));
        // return redirect()->back();
        return redirect()->route('alunos.index');
    }else{
        return redirect()->back();
    }
           
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        
        return Aluno::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $validacao = \Validator::make($data,[
        'nome' => 'required|string|max:255',
        'data_de_nascimento' => 'required|string|max:255',
        'telefone' => 'required|string|max:9|min:9',
        'encarregado_tel' => 'required|string|max:9|min:9' 
        ]);
        if($validacao->fails()){
            return redirect()->back()->withErrors($validacao)->withInput();
        }
        // $user = auth()->user();
        // $user->artigos()->find($id)->update($data);
         
         Aluno::find($id)->update($data);
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
        Aluno::find($id)->delete();
        return redirect()->back();
    }

    


   //  public static function exportarAlunos()
   // {
   //     $user = auth()->user();
   //     if($user->admin == "S"){
   //      Excel::create('alunos',function($excel){
   //          $excel->sheet('alunos',function($sheet){
   //              $sheet->loadView('admin.alunos.index');
   //          });
   //      })->export('xlsx');
   //     }
   // }

   public function fileUpload(){
        return view('admin.alunos.upload');

        // $data = $request->all();
        // $file = Input::file('image');
        // dd($file);                     
        // $image = $request->file($_POST['image']);
        // dd($image);
        // $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
        // $destinationPath = public_path('/images');
        // $image->move($destinationPath,$input['imagename']);

   }
    
   public static function export()
   {
       return Excel::download(new AlunosExport,'alunos.xlsx');           
   }

   // public static function import(Request $request)
   // {
      
   //     if($request->has('file')){
   //          $path = $request->file('file')->getRealPath();
   //      Excel::import(new AlunosImport,$path);

   //      return redirect('/')->with('success','All Good !!');
   //      }
        
           
   // }

   function bolentim($aluno_id){        
      if(is_null($this->convert_bolentim_html($aluno_id))){
          return redirect()->back()->withErrors("ALUNO SEM TURMA CORRESPONDENTE")->withInput(); 

      }else{
        $pdf = \App::make('dompdf.wrapper');
        $pdf = new Dompdf;
        $pdf->loadHTML($this->convert_bolentim_html($aluno_id));      
        $pdf->set_paper('A4','landscape');
        $pdf->render();
        return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
        exit(0);        
      }
    }

    function dec_com_notas($aluno_id){

      $pdf = \App::make('dompdf.wrapper');
      $pdf = new Dompdf;
      $pdf->loadHTML($this->convert_declaracao_com_notas_html($aluno_id));      
      $pdf->set_paper('A4','portrait');
      $pdf->render();
      return $pdf->stream('dompdf_out.pdf',array('Attachment' => false));
      exit(0);
      
    }

  function convert_bolentim_html($aluno_id){
       // dd(Turma::avaliacoesDoAluno2($aluno_id,'S'));

       $user = auth()->user();       
       $aluno = Aluno::find($aluno_id);

       $turma = $aluno->turmas()->get()->first();
       if(is_null($turma)){       
           
       }else{

       // $disciplina = Disciplina::find(1);       
       $modulo = $turma->modulo()->first();
       $curso = Curso::find($modulo->curso_id);
       $disciplinas_frequentadas_em_cada_classe  = Turma::disciplinas_frequentadas_em_cada_classe($curso,'agrupar');
       $classe = Classe::find($modulo->classe_id);
       $epoca = Epoca::where('activo','S')->first();   
       $avaliacoesDoAluno = Turma::avaliacoesDoAluno2($aluno_id,'S',$disciplinas_frequentadas_em_cada_classe);      
       $aluno_turmas = $aluno->turmas()->get()->sortBy('ano_lectivo');
       $ultima_classe = $aluno_turmas->last()->modulo->classe;

       $area_formaçao = Area::find($curso->area_id); 
       $instituicao = Instituicao::all()->first();             
       
       $modulo_10 = $modulo->where('nome',$curso->acronimo . ' 10ª')->first();
       $modulo_11 = $modulo->where('nome',$curso->acronimo . ' 11ª')->first();
       $modulo_12 = $modulo->where('nome',$curso->acronimo . ' 12ª')->first();

       if(!is_null($modulo_10)){
         $turma_10 = $aluno_turmas->where('modulo_id',$modulo_10->id)->last();         
       }

       if (!is_null($modulo_11)) {
         $turma_11 = $aluno_turmas->where('modulo_id',$modulo_11->id)->last();        
       }
       if (!is_null($modulo_11)) {
         $turma_12 = $aluno_turmas->where('modulo_id',$modulo_12->id)->last();        
       }

       
       $label_10 = '';
       $label_11 = '';
       $label_12 = '';

       $numero_10 = '';
       $numero_11 = '';
       $numero_12 = '';

       $ano_lectivo_10 = '';
       $ano_lectivo_11 = '';
       $ano_lectivo_12 = '';  

       $provenienca_10 = '';
       $provenienca_11 = '';
       $provenienca_12 = '';      
      
       $turma_10_info = explode(' ',$turma_10->nome);       
       $label_10 = $turma_10_info[2];
       $numero_10 = $turma_10->pivot->numero;      
       $provenienca_10 = $turma_10->pivot->provenienca;
       $provenienca_10 = $provenienca_10 == '' ? $instituicao->nome : $provenienca_10; 
       $ano_lectivo_10 = $turma_10->ano_lectivo;
       
       if(isset($turma_11) && !is_null($turma_11)){
         $turma_11_info = explode(' ',$turma_11->nome);
         $label_11 = $turma_11_info[2];
         $numero_11 = $turma_11->pivot->numero;
         $ano_lectivo_11 = $turma_11->ano_lectivo;
         $provenienca_11 = $turma_11->pivot->provenienca;
         $provenienca_11 = $provenienca_11 == '' ? $instituicao->nome : $provenienca_11;       
       }

       if(isset($turma_11) && !is_null($turma_12)){
         $turma_12_info = explode(' ',$turma_12->nome);
         $label_12 = $turma_12_info[2];
         $numero_12 = $turma_12->pivot->numero; 
         $ano_lectivo_12 = $turma_12->ano_lectivo;         
         $provenienca_12 = $turma_12->pivot->provenienca;
         $provenienca_12 = $provenienca_12 == '' ? $instituicao->nome : $provenienca_12;       
        }

       

      
       $turma_info = explode(' ', $turma->nome);       
       $modulos = $curso->Modulos()->get();
       $disciplinas = collect([]);
       $categorias = collect(['Sociocultural','Científica','Técnica, Tecnológica e Prática']);
       $diciplinas_categorizadas = collect([]);
       foreach ($modulos as $modulo){
          $disciplinas->push($modulo->disciplinas()->where('curricular','S')->where('terminal','S')->get());
       }
       
       $disciplinas = $disciplinas->collapse(); 
       $disciplinas_sociocultural = $disciplinas->where('categoria',$categorias[0]); 
       $disciplinas_cientifica = $disciplinas->where('categoria',$categorias[1]); 
       $disciplinas_pratica = $disciplinas->where('categoria',$categorias[2]);       
       $listaModelo = collect([]);        
       
       foreach($categorias as $categoria){
               $avaliacaoCat = collect([]);
               $disciplinas_categorizadas = $disciplinas->where('categoria',$categoria);

            foreach ($disciplinas_categorizadas as $disc_cat){
                $avaliacao_da_disc = collect([]);
                
                $avaliacoes = $avaliacoesDoAluno->where('disciplina_id',$disc_cat->id)->first();
                // dd($avaliacoes);
                $disc_modulos = $avaliacoes['disc_modulos'];

                $avaliacao_da_disc->put('cat',$categoria);
                if(strlen($disc_cat->nome) > 30){
                $avaliacao_da_disc->put('disciplina',$disc_cat->acronimo);

                }else{
                $avaliacao_da_disc->put('disciplina',$disc_cat->nome);
                    
                }              
                  $e = 0;
                 foreach ($modulos as $modulo){
                    $e++;
                    if($e < 4){                                        
                        $modulo_nome = explode(' ', $modulo->nome);
                        $mn = $modulo_nome[1];

                        $avaliacao_da_disc->put('ct1_'.$mn,$avaliacoes['ct1_'.$mn]);
                        $avaliacao_da_disc->put('ct2_'.$mn,$avaliacoes['ct2_'.$mn]);
                        $avaliacao_da_disc->put('ct3_'.$mn,$avaliacoes['ct3_'.$mn]);
                        $avaliacao_da_disc->put('mct_'.$mn,$avaliacoes['mct_'.$mn]);
                        $avaliacao_da_disc->put('pg_'.$mn,$avaliacoes['pg_'.$mn]);
                        $avaliacao_da_disc->put('ca_'.$mn,$avaliacoes['ca_'.$mn]);                        
                        $avaliacao_da_disc->put('exame1_'.$mn,$avaliacoes['exame1_'.$mn]);
                        $avaliacao_da_disc->put('exame2_'.$mn,$avaliacoes['exame2_'.$mn]);
                        $avaliacao_da_disc->put('exame3_'.$mn,$avaliacoes['exame3_'.$mn]);
                        $avaliacao_da_disc->put('cfd_'.$mn,$avaliacoes['cfd_'.$mn]);

                        
                    }
                }      
                
            $avaliacaoCat->push($avaliacao_da_disc);                                
            }
            $listaModelo->push($avaliacaoCat);            
        } 

            
                 
                 

      
  $output =" 
      <!DOCTYPE html>
      <html>
      <head>
      <title><span style='text-transform:uppercase;'>FICHA DE REGISTO DE DADOS BIOGRÁFICOS E ACADÊMICOS DE $aluno->nome</span></title>      
      <style>

          html{
            font-family: 'Arial', Sans-serif;
            font-family: 'Times New Roman', Times, serif;
          }
         .vermelhado td{
          color:red;
          border: solid 1px black;
         }
           .texto-vertical{
              -ms-transform: rotate(60deg); /* IE 9 */
              -webkit-transform: rotate(60deg); /* Safari 3-8 */
               transform: rotate(60deg);
               font-weight:bold;
               font-size:11px;
           }         
         .centro {
          text-align:center;
         }
        #cabecalho,#seccao_topo_grupo,#rodape #pedagogico{
          text-transform: uppercase;
          font-size:11px;
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
          top:20px;
       }
       #seccao_topo_grupo > div{
          float:left;
          width:33%;          
       }
       #seccao_topo_grupo #seccao_1{
          text-align:center;
       }
        #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-12px;
      }
      #bioarea{
        position:relative;
        top:50px;
        width:97%;
        border: 1px solid black;
        margin: 0 auto;
        font-size:12px;

      }
      #bioarea p{
        margin-left:2px;
     }
      #bioarea #contact_area{
        position:relative;
        float:right;top:-40px;
        padding:2px;margin-right:20px;
      }
       #rodape{;
          position:relative;          
          top:9%;
       }
       #rodape p{
         margin-top:-10px;
       }
       #rodape > div{
          float:left;
          width:50%;
          margin-top:-12px;
       }       
       #mytable{
       width:100%;
       border-collapse:collapse;
       font-size:11px;

      }
       #tabela_area{
        background:transparent;
        position:relative;
        top:45px;
      }
      #mytable>th{
        text-align:center;
      }
      th{
        text-align:center;
      }
      th,td{
        border:1px solid;
        padding: 2px;
        padding-left: 1px;
        margin:-300px;        
      }
      </style>
      </head>
      <body onload='atribueCor()'>
          <div tamanho='12'>
           <div id='cabecalho' align='center' style='font-size: 12px;font-weight: bold;class='table-responsive text-uppercase'>

            <p>REPÚBLICA DE ANGOLA</p>                                                              
            <p>MINISTÉRIO DA EDUCAÇÃO</p>                                                           
            <p>INSTITUTO POLITÉCNICO DE  CABINDA</p>                                            
            <p>FICHA DE REGISTO DE DADOS BIOGRÁFICOS E ACADÊMICOS</p>                          
            <p style='color:red;text-transform:uppercase'>$instituicao->nome</p>

            <span style='position:abolute;border:1px solid black;width:80px;height:20px;float:right;margin-right:14px;top:1px;'>

            </span>
            </div>
            <div id='seccao_topo_grupo'>    
                <div id='seccao_1'>
                   <p>ÁREA DE FORMAÇÃO: $area_formaçao->nome</p>               
                </div>
                <div id='seccao_2' align='center'>CURSO:$curso->nome
                </div>
                <div id='seccao_3' align='center'>
                   <p>
                     <span style='font-weight: bold;'>PROCESSO Nº:</span>
                     <span style='color:red;'>$aluno->idmatricula</span>
                   </p>     
                </div>
            </div>
            <div id='bioarea'>    
                <div>
                   <p>NOME: <span style='color:red'>$aluno->nome</span>, filho(a) de 
                   $aluno->pai e de $aluno->mae, natural de $aluno->naturalidade, 
                   nascido(a) aos $aluno->data_de_nascimento</p>               
                </div>
                <div>
                    <div>
                       <p>B.I.: Nº <span style='font-weight:bold'>$aluno->doc_numero</span>,
                        Emitido em $aluno->doc_local_emissao aos $aluno->doc_data_emissao, 
                        Modada: $aluno->morada
                       </p>               
                    </div>
                    <div id='contact_area'>
                       <p>TELEFONE:$aluno->telefone</p>               
                    </div>
                </div>                
            </div>
            <div id='tabela_area'>
              <table id='mytable' class='table table-bordered table-xs table-condensed' style='font-size:8px;padding:15px;'>
                  <thead>
                    <tr>      
                      <th scope='col' colspan='2' rowspan='5' style='text-align:center;font-size:11;'>DISCIPLINAS</th>      
                      <th scope='col' colspan='10'>10º CLASSE</th>      
                      <th scope='col' colspan='10'>11º CLASSE</th>      
                      <th scope='col' colspan='10'>12º CLASSE</th>      
                    </tr>
                    <tr>

                      <th scope='col' scope='col' colspan='10'>TURMA:$label_10   Nº$numero_10 ANOLECTIVO $ano_lectivo_10</th>     
                      <th scope='col' scope='col' colspan='10'>TURMA:$label_11   Nº$numero_11 ANOLECTIVO $ano_lectivo_11</th>     
                      <th scope='col' scope='col' colspan='10'>TURMA:$label_12   Nº$numero_12 ANOLECTIVO $ano_lectivo_12</th>     
                    </tr>
                    <tr style='text-transform:uppercase;'>      
                      <th scope='col' scope='col' colspan='10'>ESCOLA DE PROVENIÊNCIA: $provenienca_10</th>     
                      <th scope='col' scope='col' colspan='10'>ESCOLA DE PROVENIÊNCIA: $provenienca_11</th>     
                      <th scope='col' scope='col' colspan='10'>ESCOLA DE PROVENIÊNCIA: $provenienca_12</th>     
                    </tr>
                    <tr>      
                      <th scope='col' colspan='3'>TRIMESTRE</th>
                      <th scope='col' rowspan='2'><p class='texto-vertical'>MCT</p></th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>PG</p></th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>CA</p></th>     
                      <th scope='col' colspan='3'>EXAMES</th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>CFD</p></th> 
                      <th scope='col' colspan='3'>TRIMESTRE</th>
                      <th scope='col' rowspan='2'><p class='texto-vertical'>MCT</p></th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>PG</p></th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>CA</p></th>     
                      <th scope='col' colspan='3'>EXAMES</th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>CFD</p></th>  
                      <th scope='col' colspan='3'>TRIMESTRE</th>
                      <th scope='col' rowspan='2'><p class='texto-vertical'>MCT</p></th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>PG</p></th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>CA</p></th>     
                      <th scope='col' colspan='3'>EXAMES</th>     
                      <th scope='col' rowspan='2'><p class='texto-vertical'>CFD</p></th>     
                    </tr>      
                    <tr>      
                      <th scope='col'>1º</th>     
                      <th scope='col'>2º</th>     
                      <th scope='col'>3º</th> 
                      <th scope='col'>1º</th>     
                      <th scope='col'>2º</th>     
                      <th scope='col'>3º</th> 
                      <th scope='col'>1º</th>     
                      <th scope='col'>2º</th>     
                      <th scope='col'>3º</th> 
                      <th scope='col'>1º</th>     
                      <th scope='col'>2º</th>     
                      <th scope='col'>3º</th> 
                      <th scope='col'>1º</th>     
                      <th scope='col'>2º</th>     
                      <th scope='col'>3º</th> 
                      <th scope='col'>1º</th>     
                      <th scope='col'>2º</th>     
                      <th scope='col'>3º</th>     
                    </tr>
                </thead>
            <tbody>";
        $counter = 0;    
        $counter2 = 0;    
        $fundo = '';    
        
        foreach ($listaModelo as $lista){

            $y = 0;
            foreach($lista as $categoria){                                            
            $i = 0;                     
            $output .="
            <tr style='font-size: 12px;background:$fundo;'>
                ";
                foreach ($categoria as $key => $value){
                  if((($i == 0 && $y == 0) && $value == $categorias[0]) || (($i == 0 && $y == 0) && $value == $categorias[1]) || (($i == 0 && $y == 0) && $value == $categorias[2])){
                      $rowspan = intVal(sizeof($lista));                        
                      $output .="
                      <td style='text-align:center;text-transform:uppercase;' rowspan='$rowspan'>Componente $value</td>";
                       
                    $i++; 
                  }else if($value != $categorias[0] && $value != $categorias[1] 
                        && $value != $categorias[2]){

                    $key_value = explode('_', $key);                    
                    
                    if($key_value[0] == 'ca' || $key_value[0] == 'ct1' || $key_value[0] == 'ct2' || $key_value[0] == 'ca' || $key_value[0] == 'exame1' || $key_value[0] == 'exame2' || $key_value[0] == 'exame3' || $key_value[0] == 'cfd'){
                        if($value < 10){
                            $cor = 'red';
                        }else{
                            $cor = 'black';

                        }                        
                      $output .="
                      <td class='centro'><span style='color:$cor;'>$value</span></td>";
                    
                    }else if($key_value[0] == 'mct'){
                        if($value < 6){
                            $cor = 'red';
                        }else{
                            $cor = 'black';

                        }                        
                      $output .="
                      <td class='centro'><span style='color:$cor;'>$value</span></td>";
                    
                    }else if($key_value[0] == 'pg'){
                        if($value < 4){
                            $cor = 'red';
                        }else{
                            $cor = 'black';

                        }                        
                      $output .="
                      <td class='centro'><span style='color:$cor;'>$value</span></td>";
                    
                    }else if($key != 'disciplina'){
                      $output .="
                      <td class='centro'>$value</td>";
                    }else{
                      $output .="
                      <td>$value</td>";                        

                    }

                                      
                  } 
                $y++;          
                }
             }
            }
            
            $output .="
            </tr>
            <tr>
            <td style='font-weight:bold;text-align:center;text-transform:uppercase;' colspan='2'>SITUAÇÃO FINAL DO ALUNO b)</td>";

             if($avaliacoesDoAluno['Result_10'] == 'Trans.'){
                $result_10 = 'TRANSITA';
                $cor = 'black';
            }else if($avaliacoesDoAluno['Result_10'] == 'n/Trans.'){
                $result_10 = 'NÃO TRANSITA';
                $cor = 'red';
                if($ultima_classe->nome == '11ª'){
                   $result_10 = 'TRANSITA';
                   $cor = 'black';
                }
            }else{
                $result_10 = '';
            }
             $output .="
            <td style='text-align:center;text-transform:uppercase;' colspan='10'><span style='color:$cor;font-weight:bold;'>$result_10</span></td>";
            
            if($avaliacoesDoAluno['Result_11'] == 'Trans.'){
                $result_11 = 'TRANSITA';
                $cor = 'black';
            }else if($avaliacoesDoAluno['Result_11'] == 'n/Trans.'){
                $result_11 = 'NÃO TRANSITA';
                $cor = 'red';
                if($ultima_classe->nome == '12ª'){
                   $result_10 = 'TRANSITA';
                   $cor = 'black';
                }
            }else{
                $result_11 = '';
            }

             $output .="
            <td style='font-weight:bold;text-align:center;text-transform:uppercase;' colspan='10'><span style='color:$cor;font-weight:bold;'>$result_11</span></td>";

            if($avaliacoesDoAluno['Result_12'] == 'Trans.'){
                $result_12 = 'TRANSITA';
                $cor = 'black';
            }else if($avaliacoesDoAluno['Result_12'] == 'n/Trans.'){
                $result_12 = 'NÃO TRANSITA';
                $cor = 'red';                
            }else{
                $result_12 = '';
            }
             $output .="
            <td style='text-align:center;text-transform:uppercase;' colspan='10'><span style='color:$cor;font-weight:bold;'>$result_12</span></td>
            </tr>
            <tr>
            <td style='text-align:center;text-transform:uppercase;' colspan='2'>O COORDENADOR DO CURSO</td>
            <td style='text-align:center;text-transform:uppercase;' colspan='10'></td>
            <td style='text-align:center;text-transform:uppercase;' colspan='10'></td>
            <td style='text-align:center;text-transform:uppercase;' colspan='10'></td>            
            ";
            $counter = 0;         
        
    $output .=" 
  </tbody>
</table>
</div>

<div id='rodape' align='center'>
      <div style='font-size:11px;width:80%'>
       <p>Legenda:            a)MCT -Média de Classificação Trimestral;            PG - Prova Global;                      CA - Classificação Anual;                         CFD - Classificação Final da Disciplina.                                               
       <p>b) Transita;        Retido;          Aprovado;         Não Aprovado;         Anulou a Matrícula;        Transferido;         Excluido por Excesso de Faltas.</p> 
       <p>Colégio Padre Builo - Cabinda-Angola  Telef. $instituicao->telefone1 / $instituicao->telefone2 / $instituicao->telefone3 Correio Electrónico: $instituicao->email</p>
      </div>      
      <div id='pedagogico' style='width:20%,text-transform:uppercase'>
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>$instituicao->director_pedagogico</p>
      </div>    
    </body>
</html";

      return $output;
      } 

  }





   function convert_declaracao_com_notas_html($aluno_id){
       set_time_limit(2*60);
       $user = auth()->user();       
       $aluno = Aluno::find($aluno_id);
       $aluno_turmas = $aluno->turmas()->get()->sortBy('ano_lectivo');
       $turma = $aluno_turmas->last();
       $disciplina = Disciplina::find(1);       
       $modulo = $turma->modulo()->first();
       $instituicao = Instituicao::all()->first();

       $curso = Curso::find($modulo->curso_id);
       $disciplinas_frequentadas_em_cada_classe = Turma::disciplinas_frequentadas_em_cada_classe($curso,'agrupar');
       $classe = Classe::find($modulo->classe_id);
       $epoca = Epoca::where('activo','S')->first();   
       $avaliacoesDoAluno = Turma::avaliacoesDoAluno2($aluno_id,'S',$disciplinas_frequentadas_em_cada_classe);       
       $turma_info = explode(' ', $turma->nome);
        $classes ="";
        $anos ="";
        $k = 0;
       $aluno_ultima_turma = $aluno_turmas->last();
       $aluno_ultimo_numero = $aluno_ultima_turma->pivot->numero;
       $aluno_ultima_turma_info = explode(' ', $aluno_ultima_turma->nome);      
       $aluno_ultima_turma_nome = $aluno_ultima_turma_info[2];      
       
       foreach ($aluno_turmas as $aluno_turma){
        $k++;

        $modulo = Modulo::find($aluno_turma->modulo_id);
        $classe = Classe::find($modulo->classe_id);
        $separador = $k==(sizeof($aluno_turmas) - 1) ? ' e': ($k==sizeof($aluno_turmas)  ? '':',');         
        $classes .=" $classe->nome($classe->por_extenso)$separador";
        $anos .=" $aluno_turma->ano_lectivo$separador";
        }        
       
       $modulos = $curso->Modulos()->get();
       $disciplinas = collect([]);
       $categorias = collect(['Sociocultural','Científica','Técnica, Tecnológica e Prática']);
       $diciplinas_categorizadas = collect([]);
       foreach ($modulos as $modulo){
          $disciplinas->push($modulo->disciplinas()->where('curricular','S')->where('terminal','S')->get());
       }
       
       $modulo_10 = Modulo::where('nome',$curso->acronimo . ' 10ª')->first();
       $modulo_11 = Modulo::where('nome',$curso->acronimo . ' 11ª')->first();      
       $modulo_12 = Modulo::where('nome',$curso->acronimo . ' 12ª')->first();

       $disciplinas_10 = $modulo_10->disciplinas()->get();
       $disciplinas_11 = $modulo_11->disciplinas()->get();
       $disciplinas_12 = $modulo_12->disciplinas()->get();

       $disciplinas = $disciplinas->collapse(); 
       $disciplinas_sociocultural = $disciplinas->where('categoria',$categorias[0]); 
       $disciplinas_cientifica = $disciplinas->where('categoria',$categorias[1]); 
       $disciplinas_pratica = $disciplinas->where('categoria',$categorias[2]);       
       $listaModelo = collect([]); 
       // dd($aluno->nome);  
       $breaker ="=";
       $breaker_max = 85;    
       
       foreach($categorias as $categoria){
               $avaliacaoCat = collect([]);
               $disciplinas_categorizadas = $disciplinas->where('categoria',$categoria);

            foreach ($disciplinas_categorizadas as $disc_cat){
                $avaliacao_da_disc = collect([]);
                
                $avaliacoes = $avaliacoesDoAluno->where('disciplina_id',$disc_cat->id)->first();
                // dd($avaliacoes);
                $disc_modulos = $avaliacoes['disc_modulos'];

                $avaliacao_da_disc->put('cat',$categoria);               
                $avaliacao_da_disc->put('disciplina',$disc_cat->nome);
                  $e = 0;
                $exame = '';
                $cfd = '';

                $mods = ['10ª','11ª','12ª'];           

                foreach ($mods as $mn) {

                if($avaliacoes['cfd_'.$mn] != '' && $avaliacoes['cfd_'.$mn] != '-'){               
                    $exame = $avaliacoes['exame1_'.$mn];
                    $cfd = $avaliacoes['cfd_'.$mn];
                }
                                                            
                } 
                                                       

                $avaliacao_da_disc->put('ca_10ª',$avaliacoes['ca_10ª']);                                              
                $avaliacao_da_disc->put('ca_11ª',$avaliacoes['ca_11ª']);                                              
                $avaliacao_da_disc->put('ca_12ª',$avaliacoes['ca_12ª']);                              
                                                             
                $avaliacao_da_disc->put('exame',$exame);
                $avaliacao_da_disc->put('cfd',$cfd);                

                     
                
            $avaliacaoCat->push($avaliacao_da_disc);                                      
            }
            $listaModelo->push($avaliacaoCat); 
        }     
                      

                            
      
  $output =" 
      <!DOCTYPE html>
      <html>
      <head>
      <title><span style='text-transform:uppercase;'>DECLARAÇÃO DE ESTUDOS DE $aluno->nome</span></title>      
      <style>
         .vermelhado td{
          color:red;
          border: solid 1px black;
         }         
         .centro {
          text-align:center;
         }
        #seccao_topo_grupo,#rodape #pedagogico{
          text-transform: uppercase;
          font-size:11px;
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
          top:20px;
       }
       #seccao_topo_grupo > div{
          float:left;
          width:33%;          
       }
       #seccao_topo_grupo #seccao_1{
          text-align:center;
       }
        #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
        margin-bottom:-12px;
      }
      #dec_bio_info{
        position:relative;
        top:50px;
        width:97%;        
        margin: 0 auto;
        font-size:14px;

      }
      #dec_bio_info p{
        margin-left:2px;
     }
      #dec_bio_info #contact_area{
        position:relative;
        float:right;top:-40px;
        padding:2px;margin-right:20px;
      }
       #rodape{;
          position:relative;          
          top:9%;
       }
       #rodape p{
         margin-top:-13px;
       }
       #rodape > div{
          float:left;
          width:50%;
       }       
       #mytable{
       width:100%;
       border-collapse:collapse;
       font-size:11px;

      }
       #tabela_area{
        background:transparent;
        position:relative;
        top:45px;
      }
      #mytable>th{
        text-align:center;
      }
      #mytable.th{
        text-align:center;
      }
      #mytable th,#mytable td{
        border:1px solid;
        padding: 2px;
        padding-left: 1px;
        margin:-300px;        
      }
      thx,tdx{
        border:1px solid;
        padding: 2px;
        padding-left: 1px;
        margin:-300px;        
      }
      </style>
      </head>
      <body onload='console.log('Hello')'>
          <div tamanho='12'>
           <div id='cabecalho' align='center' style='font-size: 12px;font-weight: bold;class='table-responsive'>

            <p>REPÚBLICA DE ANGOLA</p>                                                              
            <p>MINISTÉRIO DA EDUCAÇÃO</p>                                                           
            <p style='font-weight:bold;text-align:center;font-size:14'>Instituto Politécnico de  Cabinda</p>                                            
            <p style='text-transform:uppercase;font-weight:bold;text-align:center;font-size:18'>DECLARAÇÃO DE ESTUDOS Nº</p>                          
            <p style='text-transform:uppercase;font-weight:bold;text-align:center;font-size:18'>$curso->acronimo/$instituicao->sigla/$aluno_ultima_turma->ano_lectivo</p>                          
            <p style='font-weight:bold;text-align:center;font-size:18'>$instituicao->nome</p>            
            </div>

            <div id='dec_bio_info'>    
            <table>
              <tr>
                <td>
                 <p style='text-align:justify;'>
                  ===
                   <span style='color:red;text-transform:uppercase;font-weight:bold;'>$curso->director_instituto_mae</span>,                   
                  DIRECTOR(A) DO <span style='color:black;text-transform:uppercase;'>$curso->nome_instituto_mae.</span>";
                    $wordslength = $breaker_max-14-8-strlen($curso->director_instituto_mae)-strlen($curso->nome_instituto_mae);          
                    $x = 0;
                  for ($i = $wordslength; $i > 0; $i--) {
                      $output .= $breaker;                      
                  }
                  if(strlen($curso->director_instituto_mae) >= 26){
                    $wordslength = $breaker_max + $breaker_max-strlen($curso->nome_instituto_mae)+8-strlen($curso->director_instituto_mae)-strlen($curso->nome_instituto_mae);          

                  for ($i = $wordslength; $i > 0; $i--) {
                      $output .= $breaker;
                  }

                  }
                   $output .="
                    ======== Declara, em cumprimento do despacho exarado no requerimento 
                    que fica arquivado na Secretaria deste Instituto que,<span style='color:red;font-weight:bold;'>$aluno->nome</span>.
                    ";
                    $wordslength = $breaker_max-8-strlen($aluno->nome);                    

                  for ($i = $wordslength; $i > 0; $i--) {
                      $output .= $breaker;
                  }
                  $output .="                  
                     natural de <span style='font-weight:bold;'>$aluno->naturalidade</span>, nascido(a)  
                     aos <span style='font-weight:bold;'>$aluno->data_de_nascimento</span> 
                     filho(a) de <span style='font-weight:bold;'>$aluno->pai</span> e de 
                     <span style='font-weight:bold;'>$aluno->mae</span>, portador(a) do Bilhete de Identidade 
                     nº <span style='font-weight:bold;'>$aluno->doc_numero</span>, passado pelo 
                     <span style='font-weight:bold;'>$aluno->doc_local_emissao</span> aos 
                     <span style='font-weight:bold;'>$aluno->doc_data_emissao</span>, 
                     frequentou com aproveitamento, a <span style='color:red;font-weight:bold;'>$classes</span> classes, curso médio de <span style='color:red;font-weight:bold;'>$curso->nome</span>, no ano lectivo <span style='color:red;font-weight:bold;'>$anos</span> sob número de processo 
                     <span style='font-weight:bold;'>$aluno->idmatricula</span> turma – <span style='font-weight:bold;'>$aluno_ultima_turma_nome</span>, nº <span style='font-weight:bold;'>$aluno_ultimo_numero</span>,  turno <span style='font-weight:bold;'>$aluno_ultima_turma->periodo</span> tendo obtido as seguintes classificações Anuais:</p>
                
                  </p>
                </td>
              </tr>
            </table>
                          
            </div>
            <div id='tabela_area'>
              <table id='mytable' class='table table-bordered table-xs table-condensed' style='font-size:12px;padding:15px;'>
                  <thead>
                    <tr>      
                      <th scope='col' rowspan='2' style='text-align:center;font-size:11;'>DISCIPLINAS</th>      
                      <th scope='col' colspan='5'>CLASSICAÇÕES</th>      
                           
                    </tr>
                    <tr>      
                      <th scope='col'>10º</th>     
                      <th scope='col'>11º</th>     
                      <th scope='col'>12º</th>     
                      <th scope='col'>EXAME</th>                                  
                      <th scope='col'>CFD</th>     
                    </tr>                          
                </thead>
            <tbody>";
        $counter = 0;    
        $counter2 = 0;    
        $fundo = '';    
        
        foreach ($listaModelo as $lista){            
            foreach($lista as $value){
                $disciplina = $value['disciplina'];
                $exame = $value['exame'];
                $cfd = $value['cfd'];
                $cor = 'black';
                $output .="
                <tr>
                <td style='font-size:14px;font-weight:bold;'>$disciplina</td>";
               
               $modulos = ['10ª','11ª','12ª'];
               foreach ($modulos as $mn){                
                
                $ca = $value['ca_'.$mn];                

                $cor = Avaliacao::neg_dez($ca);
                $output .="
                <td class='centro'><span style='color:$cor;font-size:10'>$ca</span></td>";             
              } 
                $cor = Avaliacao::neg_dez($exame);
                $output .="
                <td class='centro'><span style='color:$cor;font-size:10'>$exame</span></td>";
                $cor = Avaliacao::neg_dez($cfd);
                $output .="
                <td class='centro'><span style='color:$cor;font-size:10'>$cfd</span></td>
                ";
            
             }
            }
            $counter = 0;         
        
    $output .=" 
  </tbody>
</table>
</div>

<div id='rodape' style='width:90%;margin:0 auto;'>
     <p style='text-align:justify;'> ============ Por ser verdade e me ter sido solicitada, mandei passar a presente declaração que assino e autentico com o Selo Branco em uso neste Instituto, ======================= </p>

     <p style='text-align:justify;'>==== INSTITUTO POLITÉCNICO DE  CABINDA AOS " . Date('d') ." DE <span style='text-transform:uppercase;'>" . Date('M') ."</span> ". Date('Y') . " =======</p> 

     <p style='text-transform:uppercase;font-weight:bold;text-align:center;'>A DIRECTORA DO INSTITUTO</p>
     
     <p style='text-transform:uppercase;font-weight:bold;text-align:center'>$curso->director_instituto_mae
     </p></br>
     <p style='text-align:center'>OBS: Esta declaração é válida para todos os efeitos e só a original</p>


      </div>    
    </body>
</html";

 $output .= "
      <script>
        alert('Hello');
      <script>

 ";

      return $output;
       

  } 
}