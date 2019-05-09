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

    <painel titulo="Lista de Epocas">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Activo','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/epocas/" editar="/admin/epocas/" 
      deletar="/admin/epocas/" token="{{csrf_token()}}"
      modal="sim"

      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('epocas.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="trimestre">Nome</label>
        <SELECT type="text" class="form-control" id="trimestre" name="trimestre" placeholder="Trimestre" value="{{old('trimestre')}}">
          <option value="I">I</option>
          <option value="II">II</option>
          <option value="III">III</option>
        </SELECT>
      </div> 
           
      <div class="form-group col-md-6">
          <label for="data_inicial">Data e Hora Inicial</label>
          <input type="date" name="data_inicial" id="data_inicial" class="form-control" value="{{old('data_inicial')}}">          
        </div>
        <div class="form-group col-md-6">
          <label for="data_final">Data e Hora Final</label>
          <input type="date" name="data_final" id="data_final" class="form-control" value="{{old('data_final')}}">
        </div>

        <!-- <div class="radio">
        <label>Trimestre Actual</label>
        <div class="bordered">
          <label for="S"><input type="radio" name="activo" id="S" {{(old('activo') && old('activo') == 'S' ? 'checked' : '') }} value="S">S</label>
          <label for="N"><input type="radio" name="activo" id="N" {{(old('activo') && old('activo') == 'N' ? 'checked' : '') }} value="N">N</label>          
        </div>   -->
      
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/epocas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <!-- <div class="form-group">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" v-model="$store.state.item.trimestre">
      </div> --> 
           
      <div class="form-group col-md-6">
        <label for="data_inicial">Data e Hora Inicial</label>
        <input type="datetime-local" name="data_inicial" id="data_inicial" class="form-control" v-model="$store.state.item.planned_start_time">          
      </div>
      <div class="form-group col-md-6">
        <label for="data_final">Data e Hora Final</label>
        <input type="datetime-local" name="data_final" id="data_final" class="form-control" v-model="$store.state.item.planned_end_time">
      </div>  
      <div class="radio">
        <label>Trimestre</label>
      <div class="bordered">
        <label for="S"><input type="radio" name="activo" id="S"  v-model="$store.state.item.ativo">S</label>
        <label for="N"><input type="radio" name="activo" id="N"  v-model="$store.state.item.ativo">N</label>          
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
