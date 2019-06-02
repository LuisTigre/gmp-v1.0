@extends('layouts.app')

@section('content')
  <pagina tamanho="10">
    @if($errors->all())
      <div class="alert alert-danger alert-dismissible text-center" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        @foreach ($errors->all() as $key => $value)
          <li><strong>{{$value}}</strong></li>
        @endforeach
      </div>
    @endif

    <painel titulo="Lista de Devedores">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Encarregado Tel.','Telefone']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="desc" ordemcol="1"
      criar="#criar" editar="/admin/devedores/" 
      modal="sim"

      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('devedores.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="aluno">Alunos:</label>
        <select class="form-control" id="aluno" name="aluno_id[]" multiple size="20">
            @foreach($listaAlunos as $key => $value)              
              <option {{(old('aluno_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>
      
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/devedores/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}"> 
    <div class="form-group">
        <label for="aluno">Alunos:</label>
        <select class="form-control" id="aluno" name="aluno_id[]" multiple size="20">
            @foreach($listaModelo as $key => $value)              
              <option {{(old('aluno_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>     
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Apagar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.email}}</p>
  </modal>
@endsection
