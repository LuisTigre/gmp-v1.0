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

Route::get('/admin/turmas/{id}/horario', 'admin\turmasController@horario')->name('turmas.horario')->middleware('can:professor');
Route::get('/admin/turmas/{id}/horarioPDF', 'admin\turmasController@horarioPDF')->name('turmas.horarioPDF')->middleware('can:professor');

Route::get('/admin/turmas/{id}/pauta', 'admin\turmaPautaController@pdf')->name('turmas.pauta')->middleware('can:professor');

Route::get('/admin/alunos/fileUpload', 'admin\alunosController@fileUpload')->name('alunos.upload')->middleware('can:professor');

Route::get('/admin/professors/fileUpload', 'admin\professoresController@fileUpload')->name('professors.upload')->middleware('can:eAdmin');

Route::get('/admin/disciplinas/fileUpload', 'admin\disciplinasController@fileUpload')->name('disciplinas.upload')->middleware('can:eAdmin');

Route::get('/admin/turmas/{turma_id}/disciplinas/{disc_id}/Upload', 'admin\TurmaAvaliacaoController@fileUpload')->name('avaliacoes.upload')->middleware('can:professor');

// Route::post('fileUpload',[
// 	'as' => 'image.add',
// 	'uses' => 'admin\alunosController@fileUpload' 
// ]);
Route::get('/exportAlunos','admin\alunosController@export');

Route::get('admin/alunos/importAlunos','admin\alunosController@import')->name('alunos.import');
	

Route::get('/dynamic_pdf/pdf/{id}', 'admin\turmaPautaController@pdf',['parameters'=>['index'=>'filter']]);

Route::get('admin/turmas/{id}/pautafinal', 'admin\turmaPautaController@pautafinalpdf',['parameters'=>['index'=>'filter']]);
Route::get('admin/turmas/{id}/ficha_apr', 'admin\turmaPautaController@ficha_de_aproveitamento',['parameters'=>['index'=>'filter']]);

Route::get('admin/alunos/{id}/bolentim', 'admin\alunosController@bolentim',['parameters'=>['index'=>'filter']]);

Route::get('admin/alunos/{id}/dec_com_notas', 'admin\alunosController@dec_com_notas',['parameters'=>['index'=>'filter']]);

Route::get('/dynamic_pdf/minipauta/{turma}/{disc}', 'admin\turmaavaliacaoController@pdf',['parameters'=>['index'=>'filter']]);

Route::get('/admin/aula/{id}/{tempo_id}', 'admin\aulasController@atribuirTempo',['parameters'=>['index'=>'filter']]);
Route::get('/admin/aulas/{id}/update_tempo_id', 'admin\aulasController@update_tempo_id',['parameters'=>['index'=>'filter']]);

Route::middleware(['auth'])->prefix('admin')->namespace('Admin')->group(function(){

	Route::resource('artigos','ArtigosController')->middleware('can:professor');
	Route::resource('usuarios','UsuariosController')->middleware('can:eAdmin');
	Route::resource('autores','AutoresController')->middleware('can:professor');
	Route::resource('devedores','DevedoresController')->middleware('can:professor');
	Route::resource('adm','AdminController')->middleware('can:eAdmin');
	Route::resource('horario','HorarioController')->middleware('can:eAdmin');
	Route::resource('cursos','cursosController')->middleware('can:eAdmin');
	Route::resource('areas','areasController')->middleware('can:eAdmin');
	Route::resource('disciplinas','disciplinasController')->middleware('can:eAdmin');
	Route::resource('recursos','recursosController')->middleware('can:eAdmin');
	Route::resource('turmas','turmasController')->middleware('can:professor');
	Route::resource('classes','classesController')->middleware('can:eAdmin');
	Route::resource('alunos','alunosController')->middleware('can:eAdmin');
	Route::resource('instituicaos','instituicaosController')->middleware('can:eAdmin');
	Route::resource('alunos.upload','alunosController')->middleware('can:eAdmin');
	Route::resource('professores','professoresController')->middleware('can:professor');
	Route::resource('modulos','modulosController')->middleware('can:eAdmin');
	Route::resource('modulos.disciplinas','modulodisciplinaController',['parameters'=>['index'=>'filter']])->middleware('can:eAdmin');
	Route::resource('turmas.alunos','turmaalunoController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.professors','turmaprofessorController',['parameters'=>['index'=>'filter']])->middleware('can:eAdmin');
	Route::resource('turmas.disciplinas.alunos','alunodisciplinaController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.disciplinas','turmadisciplinaController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.aulas','AulasController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('aulas','AulasController',['parameters'=>['index'=>'filter']])->middleware('can:professor');	
	Route::resource('turmas.disciplinas.avaliacaos','turmaavaliacaoController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.disciplinas.avaliacaosPDF','turmaavaliacaoPDFController',['parameters'=>['index'=>'filter']])->middleware('can:professor');	
	Route::resource('avaliacaos','turmaavaliacaoController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	Route::resource('turmas.pautaPDF','turmaPautaController',['parameters'=>['index'=>'filter']])->middleware('can:professor');
	
	Route::resource('atividades','atividadesController')->middleware('can:eAdmin');
	Route::resource('grupos','atividadesGruposController')->middleware('can:eAdmin');
	Route::resource('epocas','epocasController')->middleware('can:eAdmin');
	Route::resource('configuracoes','configuracoesController')->middleware('can:eAdmin');
	Route::resource('salas','salasController')->middleware('can:eAdmin');

	

	
});
