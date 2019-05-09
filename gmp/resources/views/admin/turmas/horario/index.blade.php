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

    <painel titulo="Horário da turma {{$turma->nome}}">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
      
      <tabela-horario
      v-bind:titulos="[['','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],['07:00 - 07:50','08:00 - 08:50','09:00 - 09:50','10:00 - 10:50','11:00 - 11:50','12:00 - 12:50'],['13:00 - 13:50','14:00 - 14:50','15:00 - 15:50','16:00 - 16:50','17:00 - 17:50']]"
      v-bind:itens="{{json_encode($listaModelo)}}"  
      ordem="desc" ordemcol="1"
      criar="#criar" detalhe="/admin/aula/" editar="/admin/aulas/" deletar="/admin/artigos/" token="{{csrf_token()}}"
      modal="sim"
      tamanho='12'
      ></tabela-horario>

     
      <div align="center">        
        
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('artigos.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="titulo">Título</label>
        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título" value="{{old('titulo')}}">
      </div>
      <div class="form-group">
        <label for="descricao">Descrição</label>
        <input type="text" class="form-control" id="descricao" name="descricao" value="{{old('descricao')}}">
      </div>
      <div class="form-group">
        <label for="conteudo">Conteudo</label>
        <textarea class="form-control"  id="conteudo" name="conteudo">{{old('conteudo')}}</textarea>
      </div> 
      <div class="form-group">
        <label for="data">Data</label>
        <input type="date" class="form-control" id="data" name="data" value="{{old('data')}}">
      </div>

    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/artigos/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="titulo">Título</label>
        <input type="text" class="form-control" id="titulo" name="titulo" v-model="$store.state.item.titulo" placeholder="Título">
      </div>
      <div class="form-group">
        <label for="descricao">Descrição</label>
        <input type="text" class="form-control" id="descricao" name="descricao" v-model="$store.state.item.descricao" placeholder="Descrição">
      </div>
      <div class="form-group">
        <label for="conteudo">Conteudo</label>
        <textarea class="form-control"  id="conteudo" name="conteudo" v-model="$store.state.item.conteudo"></textarea>
      </div> 
      <div class="form-group">
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
