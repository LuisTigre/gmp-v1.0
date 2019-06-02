<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Artigo;
use App\Curso;
use App\Disciplina;
use App\Classe;
use App\Modulo;
use App\Professor;
use App\Turma;
use App\Aluno;
use App\Sala;
use App\Area;
use App\Epoca;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaMigalhas = json_encode([        
        ["titulo"=>"Admin","url"=>""]
    ]);
        $epoca = Epoca::where('Activo','S')->first();       
        $user = auth()->user();
        $totalAutores = User::where('autor','S')->count();
        $totalAdmin = User::where('admin','S')->count();
        $totalUsuarios = User::count();
        $totalArtigos = Artigo::count();
        $totalCursos = Curso::count();
        $totalDisciplinas = Disciplina::count();
        $totalClasses = Classe::count();
        $totalModulos = Modulo::count();
        $totalProfessores = Professor::count();
        $totalDevedores = Aluno::where('devedor','S')->count();
        $totalTurmas = Turma::count();
        if($user->professor == 'S'){
            $professor = Professor::where('email',$user->email)->first();
            $professor_turmas = $professor->turmas()->where('ano_lectivo',$epoca->ano_lectivo)->get();
            $totalTurmas = $professor_turmas->count();
        }
        $totalSalas = Sala::count();
        $totalAreas = Area::count();
        // if($user->professor = 's'){
        //     $professor = Professor::where('email',$user->email)->get();            
        //  $totalTurmas = $professor->disciplinas()->get();   
        // }
        $totalAlunos = Aluno::count();        

        return view('admin',compact('listaMigalhas','totalAutores','totalUsuarios','totalArtigos','totalAdmin','totalCursos','totalDisciplinas','totalClasses','totalTurmas','totalProfessores','totalModulos','totalAlunos','totalSalas','totalAreas','totalDevedores'));
    }
}
