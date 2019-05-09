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
    return $this->hasMany('App\artigo');
   }
   public function epocas()
   {
    return $this->hasMany('App\epoca');
   }
   public function areas()
   {
    return $this->hasMany('App\area');
   }
    public function atividades()
   {
    return $this->hasMany('App\atividade');
   }
    public function atividadesGrupos()
   {
    return $this->hasMany('App\atividadeGrupo');
   }
   public function cursos()
   {
    return $this->hasMany('App\curso');
   }
   public function alunos()
   {
    return $this->hasMany('App\aluno');
   }
   public function disciplinas()
   {
    return $this->hasMany('App\disciplina');
   }
   public function classes()
   {
    return $this->hasMany('App\classe');
   }
   public function turmas()
   {
    return $this->hasMany('App\turma');
   }
   public function avaliacaos()
   {
    return $this->hasMany('App\avaliacao');
   }
   public function modulos()
   {
    return $this->hasMany('App\modulo');
   }
   public function moduloDisciplinas()
   {
    return $this->hasMany('App\moduloDisciplina');
   }
   public function salas()
   {
    return $this->hasMany('App\sala');
   }
   public function tempos()
   {
    return $this->hasMany('App\tempo');
   }
   public function aulas()
   {
    return $this->hasMany('App\aula');
   }
}
