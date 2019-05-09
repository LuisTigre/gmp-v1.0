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

    <painel titulo="Lista de Atividades">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Grupo','Inicio','Prazo','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="desc" ordemcol="1"
      criar="#criar" detalhe="/admin/atividades/" editar="/admin/atividades/" 
      deletar="/admin/atividades/" token="{{csrf_token()}}"
      modal="sim"

      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('atividades.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="grupo">Grupo</label>
        <select type="text" class="form-control" id="grupo" name="grupo"  value="{{old('grupo')}}">
          @foreach($listaGrupos as $grupo)
          <option value="{{$grupo->id}}">{{$grupo->nome}}</option>
          @endforeach
        </select>
        <span><a class="btn btn-info btn-xs " href="{{route('grupos.index')}}">Novo Grupo</a></span>&nbsp;
        <span><a class="btn btn-info btn-xs " href="{{route('epocas.index')}}">Nova Epoca</a></span>&nbsp;
      </div>
      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>
        
      <div class="radio">
          <label>Trimestre</label>
          <div class="bordered">
            <label for="I"><input type="radio" name="trimestre" id="I" {{(old('trimestre') && old('trimestre') == 'Iº' ? 'checked' : '') }} value="I">Iº</label>
            <label for="II"><input type="radio" name="trimestre" id="II" {{(old('trimestre') && old('trimestre') == 'IIº' ? 'checked' : '') }} value="II">IIº</label>
            <label for="III"><input type="radio" name="trimestre" id="III" {{(old('trimestre') && old('trimestre') == 'IIIº' ? 'checked' : '') }} value="III">IIIº</label>
          </div>
      </div>      
        <div class="form-group col-md-6">
          <label for="data_inicial">Data e Hora Inicial</label>
          <input type="date" name="prazo_inicial" id="data_inicial" class="form-control" value="{{old('data_inicial')}}">          
        </div>
        <div class="form-group col-md-6">
          <label for="data_final">Data e Hora Final</label>
          <input type="date" name="prazo_final" id="data_final" class="form-control" value="{{old('data_final')}}">
        </div>  
        <div class="form-group col-md-12">
          <label for="descricao">Descrição</label>
          <textarea name="descricao" rows="5" id="descricao" class="form-control">{{old('descricao')}}</textarea> 
        </div>      
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/atividades/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" v-model="$store.state.item.nome">
      </div>
        
      <div class="radio">
          <label>Trimestre</label>
          <div class="bordered">
            <label for="I"><input type="radio" name="trimestre" id="I" {{(old('trimestre') && old('trimestre') == 'Iº' ? 'checked' : '') }} v-model="$store.state.item.epoca">Iº</label>
            <label for="II"><input type="radio" name="trimestre" id="II" {{(old('trimestre') && old('trimestre') == 'IIº' ? 'checked' : '') }} v-model="$store.state.item.epoca">IIº</label>
            <label for="III"><input type="radio" name="trimestre" id="III" {{(old('trimestre') && old('trimestre') == 'IIIº' ? 'checked' : '') }} v-model="$store.state.item.epoca">IIIº</label>
          </div>
      </div>      
        <div class="form-group col-md-6">
          <label for="data_inicial">Data e Hora Inicial</label>
          <input type="datetime-local" name="prazo_inicial" id="data_inicial" class="form-control" v-model="$store.state.item.prazo_inicial">          
        </div>
        <div class="form-group col-md-6">
          <label for="data_final">Data e Hora Final</label>
          <input type="datetime-local" name="prazo_final" id="data_final" class="form-control" v-model="$store.state.item.prazo_final">
        </div>  
        <div class="form-group col-md-12">
          <label for="descricao">Descrição</label>
          <textarea name="descricao" rows="5" id="descricao" class="form-control">@{{$store.state.item.nome}}</textarea> 
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
