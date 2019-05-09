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

    <painel titulo="Lista de Disciplinas">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Acrónimo','Nome','Categoria','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/disciplinas/" editar="/admin/disciplinas/" 
      deletar="/admin/disciplinas/" token="{{csrf_token()}}"
      modal="sim"

      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('disciplinas.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>   
      <div class="form-group">
        <label for="acronimo">Acrónimo</label>
        <input type="text" class="form-control" id="acronimo" name="acronimo" placeholder="Acrónimo" value="{{old('acronimo')}}">
      </div>
      <div class="form-group">
        <label for="categoria">Categoria:</label>        
        <select class="form-control" id="categoria" name="categoria">
          <option {{(old('categoria') && old('categoria') == 'Sociocultural' ? 'selected' : '') }} value="Sociocultural">Sociocultural</option>
          <option {{(old('categoria') && old('categoria') == 'Científica' ? 'selected' : '') }} value="Científica">Científica</option>
          <option {{(old('categoria') && old('categoria') == 'Técnica, Tecnológica e Prática' ? 'selected' : '') }} {{(!old('categoria') ? 'selected' : '')}} value="Técnica, Tecnológica e Prática">Técnica, Tecnológica e Prática</option>
        </select>
      </div>       
    </formulario>
    <span slot="botoes">
      <a href="{{route('disciplinas.upload')}}" class="btn btn-success"><i class="glyphicon glyphicon-file"></i>Importar de Excel</a>
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/disciplinas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome2">Nome</label>
        <input type="text" class="form-control" id="nome2" name="nome" v-model="$store.state.item.nome" placeholder="Nome">
      </div>
      <div class="form-group">
        <label for="acronimo">Acrónimo</label>
        <input type="text" class="form-control" id="acronimo2" name="acronimo" v-model="$store.state.item.acronimo" placeholder="Acrónimo">
      </div>
      <div class="form-group">
        <label for="categoria">Categoria:</label>        
        <select class="form-control" id="categoria" name="categoria" v-model="$store.state.item.categoria">
          <option  value="Sociocultural">Sociocultural</option>
          <option  value="Científica">Científica</option>
          <option  value="Técnica, Tecnológica e Prática">Técnica, Tecnológica e Prática</option>
        </select>
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
