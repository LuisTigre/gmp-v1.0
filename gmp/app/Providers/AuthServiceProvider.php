<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('eAdmin',function($user){
            return ($user->admin == "S" & $user->activo == "S");
        });
        Gate::define('autor',function($user){
            return (($user->admin == "S" ? true : $user->autor == "S") & $user->activo == "S");
        });
        Gate::define('professor',function($user){
            return (($user->admin == "S" ? true : $user->professor == "S") & $user->activo == "S");
        });
        Gate::define('director_turma',function($user){
            return ($user->admin == "S" ? true : $user->director_turma == "S");
        });
        Gate::define('coordenador_curso',function($user){
            return ($user->admin == "S" ? true : $user->coordenador_curso == "S");
        });
    }
}
