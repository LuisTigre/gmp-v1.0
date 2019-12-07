<?php

namespace App\Listeners;

use App\Events\AvaliacaoChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use App\Epoca;

class CacheEstatisticaDoProfessor implements ShouldQueue
{
       
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AvaliacaoChanged  $event
     * @return void
     */
    public function handle(AvaliacaoChanged $event)
    {
       set_time_limit(60*30); 
       $epoca = Epoca::where('Activo','S')->first();
          

       // if (!Cache::has($event->professor->id . '_percentagem_de_nao_avaliados')){          
                      
          $estatistica = $event->professor->estatistica($epoca->id);        
          $nao_avaliados = $estatistica['data'][6]['total_NOTAS EM FALTA'];        
          
          cache([$event->professor->id . '_percentagem_de_nao_avaliados' => $nao_avaliados], now()->addSeconds(60*60*5));
            
             // dd(Cache::get(31 . '_percentagem_de_nao_avaliados'));
        // }
    }

     public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
