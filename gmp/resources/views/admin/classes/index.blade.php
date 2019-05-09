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

    <painel titulo="Lista de Classes">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Por Extenso','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="desc" ordemcol="1"
      criar="#criar" detalhe="/admin/classes/" editar="/admin/classes/" 
      deletar="/admin/classes/" token="{{csrf_token()}}"
      modal="sim"

      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('classes.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>
      <div class="form-group">
        <label for="por_extenso">Por Ext.</label>
        <input type="text" class="form-control" id="por_extenso" name="por_extenso" placeholder="Por extenso" value="{{old('por_extenso')}}">
      </div>

    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/classes/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome2">Nome</label>
        <input type="text" class="form-control" id="nome2" name="nome" v-model="$store.state.item.nome" placeholder="Nome">
      </div>
      <div class="form-group">
        <label for="por_extenso">Por Ext.</label>
        <input type="text" class="form-control" id="por_extenso" name="por_extenso" placeholder="Por extenso" v-model="$store.state.item.por_extenso">
      </div>
      <input type="hidden" name="user_id" value="{{$user->id}}">
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.name}}</p>
  </modal>
@endsection
