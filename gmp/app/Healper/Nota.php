<?php

namespace App\healper;
use App\Avaliacao;
use App\Epocas;
class Nota
{
   /**
   * @param $collection 
   * @return max & min
   **/
   
  public static function estatistica($collection,$epoca){
    
    $output = collect([]);
    $total_alunos = $collection->count();
    $ct = Nota::ct($epoca); 

    $notas = $collection->where($ct,'!=',null);
       $notas = $notas->sortBy($ct);

       $min = $epoca->trimestre == 'I' ? $notas->first()->ct1 : 
             ($epoca->trimestre == 'II' ? $notas->first()->ct2 : 
                                          $notas->first()->notafinal);

       $max = $epoca->trimestre == 'I' ? $notas->last()->ct1 : 
             ($epoca->trimestre == 'II' ? $notas->last()->ct2 : 
                                          $notas->last()->notafinal); 

       $valores = collect([]);
       foreach ($collection as $key => $value) {

             $epoca->trimestre == 'I' ? $valores->push($value->ct1) : 
             ($epoca->trimestre == 'II' ? $valores->push($value->ct2) : 
                                          $valores->push($value->notafinal));
       }

       $aptos = $collection->where($ct,'>=',10);  
       $naptos = $collection->where($ct,'<',10);  
       $aptos_qtd = $aptos->count();  
       $naptos_qtd = $naptos->count();
       $aptos_perc= round(($aptos->count() * 100)/$total_alunos,1);  
       $naptos_perc = round(($naptos->count() * 100)/$total_alunos,1);

       $m = $collection->where('sexo','M')->count();   
       $f = $collection->where('sexo','F')->count();   

       $media = $valores->median(); 
       $output->put('max',$max);
       $output->put('min',$min);
       $output->put('media',$media);
       $output->put('aptos',$aptos);
       $output->put('naptos',$naptos);
       $output->put('aptos_qtd',$aptos_qtd);
       $output->put('naptos_qtd',$naptos_qtd);
       $output->put('aptos_perc',$aptos_perc);
       $output->put('naptos_perc',$naptos_perc);
       $output->put('m',$m);
       $output->put('f',$f);
       
       return $output;
  
  }
  public static function estatistica_turma($collection,$total_alunos){
  		
  		$estatistica = Collect([]);
  		$titulos = ['Reprovados','Aprovados','%Reprovados','%Aprovados'];
  			
  			foreach ($titulos as $titulo) {
                $disc_stats = Collect([]);
  				$output = 0;
  				foreach ($collection as $key => $value) {
  				  if($titulo == 'Reprovados'){
                  	$output = $value;

  				  }else if($titulo == 'Aprovados'){
  				  	$reprovados = $estatistica['Reprovados'][$key];
                  	$output = $total_alunos - $reprovados;

                  }else if($titulo == '%Reprovados'){
                  	$reprovados = $estatistica['Reprovados'][$key];
                  	$output = round(($reprovados * 100)/$total_alunos,1); 
				  
				  }else if($titulo == '%Aprovados'){
				  	$aprovados = $estatistica['Aprovados'][$key];
                  	$output = round(($aprovados * 100)/$total_alunos,1); 
                  }

                  $disc_stats->put($key,$output);     
                  
  				}
                  $estatistica->put($titulo,$disc_stats);    
            }     
            
         return $estatistica;
  } 

  public static function buscar_avaliacao_trimestral($collection,$epoca){ 

  		$output = collect([]);		
 		 
 		 $ct = $epoca->trimestre == 'I' ? (is_null($collection->ct1) ? '': $collection->ct1 ): 
 		 ($epoca->trimestre == 'II' ? (is_null($collection->ct2) ? '': $collection->ct2) : 
 		 (is_null($collection->notafinal) ? '': $collection->notafinal));

 		$fnj = $epoca->trimestre == 'I' ? (is_null($collection->fnj1) ? '': $collection->fnj1 ): 
 		 ($epoca->trimestre == 'II' ? (is_null($collection->fnj2) ? '': $collection->fnj2) : 
 		 (is_null($collection->fnj3) ? '': $collection->fnj3));

        $fj = '';   
       
        $output->put('ct',$ct);
        $output->put('fnj',$fnj);
        $output->put('fj',$fj);
        
        return $output;
  } 

  public static function ct($epoca){
  	return $epoca->trimestre == 'I' ? 'ct1' : ($epoca->trimestre == 'II' ? 'ct2' : 'notafinal');
  }    

  public static function fnj($epoca){
  	return $epoca->trimestre == 'I' ? 'fnj1' : ($epoca->trimestre == 'II' ? 'fnj2' : 'fnj3');
  }   

  public static function fj($epoca){
  	return $epoca->trimestre == 'I' ? 'fj1' : ($epoca->trimestre == 'II' ? 'fj2' : 'fj3');
  }        
    
      
}
