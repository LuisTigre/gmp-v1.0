<table class="table table-bordered table-xs table-condensed" style="font-size:10px;">
  <thead>
    <tr style="font-weight: bold;">
      <th scope="col" rowspan="3" style="">#</th>  
      <th scope="col" rowspan="3" style="">Nº Mat</th>  
      <th scope="col" rowspan="3" style="">Nome</th>  
      <th scope="col" rowspan="3" style="text-orientation:vertical);">Id</th>        
      @foreach($listaCabecalho2 as $key => $value)
      <th scope="col" colspan="3" style="">{{$value->disciplina}}</th>
      @endforeach
      <th scope="col" rowspan="3" class="rotate">Gê</th>  
      <th scope="col" rowspan="3" style="">Md</th>  
      <th scope="col" rowspan="3" style="">OBS</th>  
    </tr>
    <tr>
      @foreach($listaCabecalho2 as $key => $value)
      <th scope="col" colspan="2">Faltas</th>  
      <th scope="col" rowspan="2">CT1</th>
      @endforeach          
    </tr>
    <tr>
      @foreach($listaCabecalho2 as $key => $value)
      <th scope="col">J</th>  
      <th scope="col">N</th>      
      @endforeach          
    </tr>
  </thead>
  <tbody>    
      <!-- <th scope="row">1</th>       -->
      @foreach($listaModelo['data'] as $aluno)
      <tr style="font-size: 12px;"> 
       @foreach($aluno as $value)     
        <td>{{$value}}</td>      
       @endforeach
      </tr>      
      @endforeach     
    
  </tbody>
</table>