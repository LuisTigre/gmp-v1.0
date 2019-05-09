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

    <painel titulo="Lista de Professores">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     @if ($user->professor == 'S') 
      <tabela-lista
      v-bind:titulos="['#','Nome','Telefone','E-mail']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/professores/" editar="/admin/professores/" 
      token="{{csrf_token()}}"
      modal="sim"
      ></tabela-lista>
     @elseif($user->admin == 'S') 
      <tabela-lista
      v-bind:titulos="['#','Nome','Telefone','E-mail']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/professores/" editar="/admin/professores/" 
      deletar="/admin/professores/" token="{{csrf_token()}}"
      modal="sim"
      ></tabela-lista>
      @endif
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('professores.store')}}" method="post" enctype="" token="{{csrf_token()}}">
      <input type="hidden" name="professor" value="S">
      <input type="hidden" name="activo" value="S">
      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>      
      <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone" value="{{old('telefone')}}">
      </div>      
      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="{{old('email')}}">
      </div>
      <input type="hidden" name="professor" value="S">  
      <div class="form-group">
        <label for="password">Senha</label>
        <input type="password" class="form-control" id="password" name="password" value="{{old('password')}}">
      </div>     
    </formulario>
    <span slot="botoes">
      <a href="{{route('professors.upload')}}" class="btn btn-success"><i class="glyphicon glyphicon-file"></i>Importar de Excel</a>
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/professores/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <input type="hidden" name="professor" value="S">
      <div class="form-group">
        <label for="nome2">Nome</label>
        <input type="text" class="form-control" id="nome2" name="nome" v-model="$store.state.item.nome" placeholder="Nome">
      </div>      
      <div class="form-group">
        <label for="email2">Telefone</label>
        <input type="text" class="form-control" id="email2" name="telefone" v-model="$store.state.item.telefone" placeholder="Telefone">
      </div>      
      <div class="form-group">
        <label for="email2">E-mail</label>
        <input type="email" class="form-control" id="email2" name="email" v-model="$store.state.item.email" placeholder="E-mail">
      </div>
      <input type="hidden" name="professor" value="S">
      <div class="form-group">
        <label for="password2">Senha</label>
        <input type="password" class="form-control" id="password2" name="password">
      </div>      
        
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.email}}</p>
  </modal>
@endsection
