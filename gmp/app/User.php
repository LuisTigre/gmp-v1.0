<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','autor','admin','professor','director_turma','coordenador_curso','activo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function artigos()
   {
    return $this->hasMany('App\Artigo');
   }
   public function epocas()
   {
    return $this->hasMany('App\Epoca');
   }
   public function areas()
   {
    return $this->hasMany('App\Area');
   }
    public function atividades()
   {
    return $this->hasMany('App\Atividade');
   }
    public function atividadesGrupos()
   {
    return $this->hasMany('App\AtividadeGrupo');
   }
   public function cursos()
   {
    return $this->hasMany('App\Curso');
   }
   public function alunos()
   {
    return $this->hasMany('App\Aluno');
   }
   public function disciplinas()
   {
    return $this->hasMany('App\Disciplina');
   }
   public function classes()
   {
    return $this->hasMany('App\Classe');
   }
   public function turmas()
   {
    return $this->hasMany('App\Turma');
   }
   public function avaliacaos()
   {
    return $this->hasMany('App\Avaliacao');
   }
   public function modulos()
   {
    return $this->hasMany('App\Modulo');
   }
   public function moduloDisciplinas()
   {
    return $this->hasMany('App\ModuloDisciplina');
   }
   public function salas()
   {
    return $this->hasMany('App\Sala');
   }
   public function tempos()
   {
    return $this->hasMany('App\Tempo');
   }
   public function aulas()
   {
    return $this->hasMany('App\Aula');
   }
}
