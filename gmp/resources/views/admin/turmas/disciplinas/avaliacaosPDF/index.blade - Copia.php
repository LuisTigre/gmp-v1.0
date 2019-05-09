@extends('layouts.app')

@section('content')
  <pagina tamanho="12">
   <div id="cabecaho" align="center" style="font-size: 12px;font-weight: bold;" class="table-responsive text-uppercase">
      <p>COLÉGIO PADRE BUILU</p>                           
      <p>FÉ E CIÊNCIA</p>                                       
      <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
      <p>PAUTA DE APROVEITAMENTO</p>            
      <p>ÁREA DE FORMAÇÃO: {{$curso->nome}}</p>           
      <p style="color:red;">REGIME DIURNO ( {{$turma->periodo}} )</p> 
    </div>
    <div class="col-md-3">
       <p>ANO LECTIVO {{$turma->ano_lectivo}}</p> 
       <p>{{$classe->nome}} CLASSE TURMA: {{$turma_info[2]}} </p>         
    </div>
    <div align="center" class="col-md-6">
       <p><span style="font-weight: bold;">DISCIPLINA:</span> <span style="color:red;">{{$disciplina->acronimo}}</span> ({{$disciplina->nome}})</p>     
    </div>

      <table class="table table-bordered table-xs table-condensed" style="font-size:10px;">
  <thead>
    <tr style="font-weight: bold;">
      <th scope="col" rowspan="2" style="">#</th>    
      <th scope="col" rowspan="2" style="">Nome completo</th>  
      <th scope="col" rowspan="2" style="text-orientation:vertical);">Id</th>       <th scope="col" rowspan="2" style="text-orientation:vertical);">F</th>        
      
      <th scope="col" colspan="4" style="">I TRIMESTRE</th>
      <th scope="col" rowspan="2">F</th>  
      <th scope="col" colspan="6" style="">II TRIMESTRE</th>
      <th scope="col" rowspan="2">F</th>  
      <th scope="col" colspan="5" style="">III TRIMESTRE</th>     
      <th scope="col" colspan="6" style="">Classificação Anual</th> 
    </tr>
    <tr>      
      <th scope="col">Mac</th>  
      <th scope="col">P1</th>      
      <th scope="col">P2</th>      
      <th scope="col">CT1</th>  
      <th scope="col">Mac</th>  
      <th scope="col">P1</th>      
      <th scope="col">P2</th>      
      <th scope="col">CF2</th>      
      <th scope="col">CT1</th>
      <th scope="col">CT2</th>      
      <th scope="col">Mac</th>  
      <th scope="col">P1</th>      
      <th scope="col">CF3</th>      
      <th scope="col">CT2</th>      
      <th scope="col">CT3</th>  
      <th scope="col">MTC</th>      
      <th scope="col">60%</th>      
      <th scope="col">PG</th>      
      <th scope="col">40%</th>      
      <th scope="col">60% + 40%</th>    
    </tr>    
  </thead>
  <tbody>    
      <!-- <th scope="row">1</th>       -->
      
        @foreach($listaModelo as $aluno)
      <tr style="font-size: 12px;">
         <td>{{$aluno->numero}}</td>   
         <td>{{$aluno->nome}}</td>   
         <td>{{$aluno->idade}}</td>   
         <!-- I TRIMESTRE -->
         <td>{{$aluno->fnj1}}</td>     
         <td>{{$aluno->mac1}}</td>     
         <td>{{$aluno->p11}}</td>     
         <td>{{$aluno->p12}}</td>     
         <td>{{$aluno->ct1}}</td>
         <!-- II TRIMESTRE -->
         <td>{{$aluno->fnj2}}</td>     
         <td>{{$aluno->mac2}}</td>     
         <td>{{$aluno->p21}}</td>     
         <td>{{$aluno->p22}}</td>     
         <td>{{$aluno->cf2}}</td>     
         <td>{{$aluno->ct1}}</td>     
         <td>{{$aluno->ct2}}</td> 
         <!-- III TRIMESTRE -->
         <td>{{$aluno->fnj3}}</td>     
         <td>{{$aluno->mac3}}</td>     
         <td>{{$aluno->p31}}</td>     
         <td>{{$aluno->cf3}}</td>     
         <td>{{$aluno->ct2}}</td>     
         <td>{{$aluno->ct3}}</td>     
         <!-- classificacao anual -->
         <td>{{$aluno->mtc}}</td>     
         <td>{{$aluno->sessenta}}</td>     
         <td>{{$aluno->p32}}</td>     
         <td>{{$aluno->quarenta}}</td>     
         <td>{{$aluno->notafinal}}</td>     
      </tr>      
        @endforeach      
      
      
    
  </tbody>
</table>

<div id="cabecaho" align="center">
      <div class="col-md-6 text-uppercase">
       <p>O(A) PROFESSOR(A) DE TURMA                  
       <p>_________________________________</p> 
       <p>{{$professor->nome}}</p> 
      </div>      
      <div class="col-md-6">
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>ERNESTO TIGRE ISSAMBO</p>
      </div>

      
  <pagina>
@endsection
