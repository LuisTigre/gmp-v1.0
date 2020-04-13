<?php
use App\Artigo;
use App\Modulo;
use App\Curso;
use App\Classe;
use App\Disciplina;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('command', function () {
	
	/* php artisan migrate */
    // \Artisan::call('migrate');
    // dd("Done");
// });

Route::get('/', function (Request $req) {
	// dd($req->busca);
	if(isset($req->busca) && $req->busca != ""){
		$busca = $req->busca;
		$lista =  Artigo::listaArtigosSite(3,$busca);
		// dd($lista);		
	}else{
	$lista = Artigo::listaArtigosSite(3); 	
	$busca = "";
		
	}

    return view('site',compact('lista','busca'));
})->name('site');

Route::get('/artigo/{id}/{titulo?}', function ($id) {
	$artigo = Artigo::find($id); 
	if($artigo){			
    	return view('artigo',compact('artigo'));
	}
	return redirect()->route('site');	
})->name('artigo');



Auth::routes();

Route::get('/admin', 'AdminController@index')->name('admin')->middleware('can:professor');

Route::get('/pdf', function(){
	$pdf = PDF::loadView('pdf');
	return $pdf->stream();
});

Route::get('/admin/turmas/{id}/horario', 'Admin\TurmasController@horario')->name('turmas.horario')->middleware('can:professor');
Route::get('/admin/turmas/{id}/horarioPDF', 'Admin\TurmasController@horarioPDF')->name('turmas.horarioPDF')->middleware('can:professor');

Route::get('/admin/turmas/{id}/pauta', 'Admin\RelatoriosController@pautaTrimestral')->name('turmas.pauta')->middleware('can:professor');

Route::get('/admin/alunos/fileUpload', 'Admin\AlunosController@fileUpload')->name('alunos.upload')->middleware('can:professor');

Route::get('/admin/professors/fileUpload', 'Admin\ProfessoresController@fileUpload')->name('professors.upload')->middleware('can:eAdmin');

Route::get('/admin/disciplinas/fileUpload', 'Admin\DisciplinasController@fileUpload')->name('disciplinas.upload')->middleware('can:eAdmin');

Route::get('/admin/turmas/{turma_id}/disciplinas/{disc_id}/Upload', 'Admin\TurmaAvaliacaoController@fileUpload')->name('avaliacoes.upload')->middleware('can:professor');

Route::get('/admin/{epoca_id}/turmas/{id}/disciplinas/{disciplina_id}/estatistica', 'Admin\TurmaDisciplinaController@estatistica')->name('professor.estatistica')->middleware('can:professor');

Route::get('/admin/professores/{id}/estatistica', 'Admin\ProfessoresController@estatistica')->name('professor.estatistica')->middleware('can:professor');



// Route::post('fileUpload',[
// 	'as' => 'image.add',
// 	'uses' => 'Admin\AlunosController@fileUpload' 
// ]);
Route::get('/exportAlunos','Admin\AlunosController@export');
Route::get('/exportInvoices','Admin\usuariosController@export');

Route::get('admin/alunos/importAlunos','Admin\AlunosController@import')->name('alunos.import');
	

Route::get('/dynamic_pdf/pdf/{id}', 'Admin\RelatoriosController@minipauta',['parameters'=>['index'=>'filter']]);

Route::get('admin/turmas/{id}/pautafinal', 'Admin\RelatoriosController@pautaAnual',['parameters'=>['index'=>'filter']]);
Route::get('admin/turmas/{id}/pautafinalhtml', 'Admin\RelatoriosController@pautaAnualhtml',['parameters'=>['index'=>'filter']]);

Route::get('admin/turmas/{id}/ficha_apr', 'Admin\RelatoriosController@ficha_de_aproveitamento',['parameters'=>['index'=>'filter']]);
Route::get('admin/alunos/{id}/bolentim', 'Admin\AlunosController@bolentim',['parameters'=>['index'=>'filter']]);

Route::get('admin/alunos/{id}/dec_com_notas', 'Admin\AlunosController@dec_com_notas',['parameters'=>['index'=>'filter']]);

Route::get('/dynamic_pdf/minipauta/{turma}/{disc}', 'Admin\RelatoriosController@minipauta',['parameters'=>['index'=>'filter']]);
Route::get('/pdf/minipauta/{turma}/{disc}', 'Admin\TurmaAvaliacaoController@myPdfMethod',['parameters'=>['index'=>'filter']]);

Route::get('/admin/aula/{id}/{tempo_id}', 'Admin\AulasController@atribuirTempo',['parameters'=>['index'=>'filter']]);
Route::get('/admin/aulas/{id}/update_tempo_id', 'Admin\AulasController@update_tempo_id',['parameters'=>['index'=>'filter']]);
Route::get('/admin/cursos/{id}/disciplinas', 'Admin\CursosController@disciplinas',['parameters'=>['index'=>'filter']]);
Route::get('/admin/professores/{id}/turmas', 'Admin\ProfessoresController@turmas',['parameters'=>['index'=>'filter']])->middleware('can:professor');
Route::get('/admin/turmas/{id}/alunos/actualizar', 'Admin\TurmaAlunoController@actualizar_num',['parameters'=>['index'=>'filter']])->middleware('can:eAdmin');
Route::get('/admin/apagar/professores/{id}/turmas/{turma_id}', 'Admin\ProfessoresController@remover_turma',['parameters'=>['index'=>'filter']])->middleware('can:professor');



Route::middleware(['auth'])->prefix('admin')->namespace('Admin')->group(function(){

	Route::resource('artigos','ArtigosController')->middleware('can:professor');
	Route::resource('usuarios','UsuariosController')->middleware('can:eAdmin');
	Route::resource('autores','AutoresController')->middleware('can:professor');
	Route::resource('devedores','DevedoresController')->middleware('can:professor');
	Route::resource('adm','AdminController')->middleware('can:eAdmin');
	// Route::resource('horarios','HorariosController')->middleware('can:eAdmin');
	Route::resource('cursos','CursosController')->middleware('can:eAdmin');
	Route::resource('areas','AreasController')->middleware('can:eAdmin');
	Route::resource('disciplinas','DisciplinasController')->middleware('can:eAdmin');
	Route::resource('recursos','RecursosController')->middleware('can:eAdmin');
	Route::resource('turmas','TurmasController')->middleware('can:professor');
	Route::resource('classes','ClassesController')->middleware('can:eAdmin');
	Route::resource('alunos','AlunosController')->middleware('can:eAdmin');
	Route::resource('instituicaos','InstituicaosController')->middleware('can:eAdmin');
	Route::resource('alunos.upload','AlunosController')->middleware('can:eAdmin');
	Route::resource('professores','ProfessoresController')->middleware('can:professor');
	Route::resource('modulos','ModulosController')->middleware('can:eAdmin');
	Route::resource('modulos.disciplinas','ModuloDisciplinaController',['parameters'=>['index'=>'filter']])->middleware('can:eAdmin');
	Route::resource('turmas.alunos','TurmaAlunoController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.professors','TurmaProfessorController',['parameters'=>['index'=>'filter']])->middleware('can:eAdmin');
	Route::resource('turmas.disciplinas.alunos','AlunoDisciplinaController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.disciplinas','TurmaDisciplinaController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.aulas','AulasController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('aulas','AulasController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.disciplinas.avaliacaos','TurmaAvaliacaoController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	// Route::resource('turmas.disciplinas.avaliacaosPDF','turmaavaliacaoPDFController',['parameters'=>['index'=>'filter']])->middleware('can:professor');	
	Route::resource('avaliacaos','TurmaAvaliacaoController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	
	
	Route::resource('atividades','AtividadesController')->middleware('can:eAdmin');
	Route::resource('grupos','AtividadesGruposController')->middleware('can:eAdmin');
	Route::resource('epocas','EpocasController')->middleware('can:eAdmin');	
	Route::resource('salas','SalasController')->middleware('can:eAdmin');	
});


      /*FUNCOES PARA  O AJAX*/
Route::get('/admin/turmas/{id}/alunos_idx', 'Admin\TurmaAlunoController@listaModelo')->name('turmas.alunos_idx')->middleware('can:professor');

Route::get('/admin/turmas/{id}/alunos/{id2}/deleteMultiple', 'Admin\TurmaAlunoController@deleteMultiple')->name('turmas.alunos_delete_multiple')->middleware('can:eAdmin');



