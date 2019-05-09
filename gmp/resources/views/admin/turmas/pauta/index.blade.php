@extends('layouts.app')

@section('content')
<pagina tamanho="12">
    @if($errors->all())
      <div class="alert alert-danger alert-dismissible text-center" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        @foreach ($errors->all() as $key => $value)
          <li><strong>{{$value}}</strong></li>
        @endforeach
      </div>
    @endif

    <painel titulo="Pauta da turma {{$turma->nome}} {{$epoca->trimestre}} trimestre {{$turma->ano_lectivo}}">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>      
        <div class="no-print text-danger">
          <!-- <a href="" @click.privent="printme" target="blank" class="btn btn-success"><i class="glyphicon glyphicon-print"></i> Imprimir</a> -->
          <a href="{{url('/dynamic_pdf/pdf/' . $turma->id)}}" class="btn btn-danger"><i class="glyphicon glyphicon-print"></i>PDF</a>    
        </div>
  
       <tabela-pauta
        v-bind:titulos="{{json_encode($listaCabecalho2)}}"
        v-bind:itens="{{json_encode($listaModelo)}}"       
        ordem="asc" ordemcol="0"
        criar="#criar" detalhe="/admin/turmas/alunos/" editar="'/admin/turmas/'+{{json_encode($turma->id)}}+'/alunos/" 
        v-bind:deletar="'/admin/turmas/'+{{json_encode($turma->id)}}+'/alunos/'" token="{{csrf_token()}}"
        modal="sim"     

     ></tabela-pauta>
    </painel>
   </pagina>
  

  
  
@endsection
