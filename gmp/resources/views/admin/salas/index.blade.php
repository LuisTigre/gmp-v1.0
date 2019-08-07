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

    <painel titulo="Lista de Salas">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Laboratório','Descrição','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/salas/" editar="/admin/salas/" 
      deletar="/admin/salas/" token="{{csrf_token()}}"
      modal="sim"

      >
       <span class="no-print text-danger">          
          <a href="/admin/turmas" class="btn btn-default btn-xs"></i>Turmas</a>       
      </span>  
      </tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar Sala">
    <formulario id="formAdicionar" css="" action="{{route('salas.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">      
      </div>

      <div class="form-group">
        <label for="laboratorio">Laboratório</label>        
        <select class="form-control" id="laboratorio" name="laboratorio">
          <option {{(old('laboratorio') && old('laboratorio') == 'S' ? 'selected' : '') }} value="S">Sim</option>
          <option {{(old('laboratorio') && old('laboratorio') == 'N' ? 'selected' : '') }} {{(!old('laboratorio') ? 'selected' : '')}} value="N">Não</option>
        </select>
      </div>    
      <div class="form-group">
        <label for="descricao">Descrição</label>
        <textarea class="form-control"  id="descricao" name="descricao">{{old('descricao')}}</textarea>
      </div>
       
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar Sala">
    <formulario id="formEditar" v-bind:action="'/admin/salas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="nome" value="{{old('nome')}}" v-model="$store.state.item.nome">      
      </div>

      <div class="form-group">
        <label for="laboratorio">Laboratório</label>        
        <select class="form-control" id="laboratorio" name="laboratorio" v-model="$store.state.item.laboratorio">
          <option value="N">Não</option>
          <option value="S">Sim</option>
        </select>
      </div>    
      <div class="form-group">
        <label for="descricao">Descrição</label>
        <textarea class="form-control"  id="descricao" name="descricao">@{{$store.state.item.descricao}}</textarea>
      </div>
       
    </formulario>    
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.name}}</p>
  </modal>
@endsection
