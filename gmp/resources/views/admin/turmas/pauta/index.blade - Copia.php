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

    <painel titulo="Pauta">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>

      <tabela-lista
      v-bind:titulos="{{json_encode($listaCabecalho)}}"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="0"
      criar="#criar" detalhe="/admin/artigos/" editar="/admin/artigos/"
      modal="sim"

      ></tabela-lista>
      <!-- <div align="center">        
        {{$listaModelo}}
      </div> -->
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="/admin/turmas/{{$turma->id}}/pauta" method="post" enctype="" token="{{csrf_token()}}">
      <input type="hidden" name="professor_id" value="{{$turma->id}}">
      <input type="hidden" name="user_id" value="">      
      <div class="form-group col-xs-12">
        <label for="nome">Nome</label>
        <select class="form-control" id="nome" name="aluno_id" value="{{old('aluno_id')}}">
        
          <option value=""></option>         
                
        </select>
      </div>
      <div class="form-group col-xs-12">
        <div><h4>I Trimestre</h4></div>     
        <div class="col-xs-2">
          <label for="mac">Mac</label>
          <input type="text" class="form-control" id="mac" name="mac1" value="{{old('mac1')}}">
        </div>
        <div class="col-xs-2">
          <label for="p1">P1</label>
          <input type="text" class="form-control" id="p1" name="p11" value="{{old('p11')}}">
        </div>
        <div class="col-xs-2">
          <label for="p2">P2</label>
          <input type="text" class="form-control" id="p2" name="p12" value="{{old('p12')}}">
        </div>
        <div class="col-xs-3">
          <label for="fnj1">Faltas</label>
          <input type="text" class="form-control" id="fnj1" placeholder="Não justificadas" name="fnj1" value="{{old('fnj1')}}">
        </div>
        <div class="col-xs-3">
          <label for="fj1">Faltas</label>
          <input type="text" class="form-control" id="fj1" placeholder="Justificadas" name="fj1" value="{{old('fj1')}}">
        </div>
      </div>

      <div class="form-group col-xs-12">
        <div><h4>II Trimestre</h4></div>
        <div class="col-xs-2">
          <label for="mac">Mac</label>
          <input type="text" class="form-control" id="mac" name="mac2" value="{{old('mac2')}}">
        </div>
        <div class="col-xs-2">
          <label for="p1">P1</label>
          <input type="text" class="form-control" id="p1" name="p21" value="{{old('p21')}}">
        </div>
        <div class="col-xs-2">
          <label for="p2">P2</label>
          <input type="text" class="form-control" id="p2" name="p22" value="{{old('p22')}}">
        </div>
        <div class="col-xs-3">
          <label for="fnj2">Faltas</label>
          <input type="text" class="form-control" id="fnj2" placeholder="Não justificadas" name="fnj2" value="{{old('fnj2')}}">
        </div>
        <div class="col-xs-3">
          <label for="fj2">Faltas</label>
          <input type="text" class="form-control" id="fj2" placeholder="Justificados" name="fj2" value="{{old('fj2')}}">
        </div>
      </div>
       
       <div class="form-group col-xs-12">
        <div><h4>III Trimestre</h4></div>   
        <div class="col-xs-2">
          <label for="mac">Mac</label>
          <input type="text" class="form-control" id="mac" name="mac3" value="{{old('mac3')}}">
        </div>
        <div class="col-xs-2">
          <label for="p1">P1</label>
          <input type="text" class="form-control" id="p1" name="p31" value="{{old('p31')}}">
        </div>
        <div class="col-xs-2">
          <label for="p32">P2</label>
          <input type="text" class="form-control" id="p32" name="p32" value="{{old('p32')}}">
        </div>
        <div class="col-xs-3">
          <label for="fnj3">Faltas</label>
          <input type="text" class="form-control" id="fnj3" placeholder="Não justificadas" name="fnj3" value="{{old('fnj2')}}">
        </div>
        <div class="col-xs-3">
          <label for="fj3">Faltas</label>
          <input type="text" class="form-control" id="fj3" placeholder="Justificados" name="fj3" value="{{old('fj3')}}">
        </div>
      </div>     
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/artigos/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group col-xs-12">
        <label for="titulo">Título</label>
        <input type="text" class="form-control" id="titulo" name="titulo" v-model="$store.state.item.titulo" placeholder="Título">
      </div>
      <div class="form-group col-xs-3">
        <label for="descricao">Descrição</label>
        <input type="text" class="form-control" id="descricao" name="descricao" v-model="$store.state.item.descricao" placeholder="Descrição">
      </div>
      <div class="form-group col-xs-3">
        <label for="conteudo">Conteudo</label>
        <textarea class="form-control"  id="conteudo" name="conteudo" v-model="$store.state.item.conteudo"></textarea>
      </div> 
      <div class="form-group col-xs-3">
        <label for="data">Data</label>
        <input type="date" class="form-control" id="data" name="data" v-model="$store.state.item.data">
      </div>
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.titulo">
    <p>@{{$store.state.item.descricao}}</p>
    <p>@{{$store.state.item.conteudo}}</p>
  </modal>
@endsection
