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

    <painel titulo="Lista de Cursos">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Acr.','Área','Coodenador','Instituição','Director','Alt.Por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="desc" ordemcol="1"
      criar="#criar" editar="itself/admin/cursos/" 
      deletar="/admin/cursos/" token="{{csrf_token()}}"
      modal="sim"
      v-bind:buttons="[{'nome':'Disciplinas','url':'/admin/cursos/' ,'action':'disciplinas'}]"
      >
      <span class="no-print text-danger">           
          <a href="/admin/classes" class="btn btn-default btn-xs"></i>classes</a>    
          <a href="/admin/areas" class="btn btn-default btn-xs"></i>Areas</a>    
      </span>        
      </tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('cursos.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      <div class="form-group col-md-12">
        <label for="nome">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>
      <div class="form-group col-md-3">
        <label for="acronimo">Acrónimo</label>
        <input type="text" class="form-control" id="acronimo" name="acronimo" placeholder="Acrónimo" value="{{old('acronimo')}}">
      </div>  
      <div class="form-group col-md-5">
        <label for="professor_id">Coordenador</label>
        <select  class="form-control" id="professor_id" name="professor_id" value="{{old('professor_id')}}">
            @foreach($listaProfessores as $key => $value)              
              <option {{(old('professor_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select> 
      </div>
      <div class="form-group col-md-4">
        <label for="area">Áreas de Formação</label>
        <select  class="form-control" id="area" name="area_id" value="{{old('area_id')}}">
            @foreach($listaAreas as $key => $value)              
              <option {{(old('area') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select> 
      </div>
      <div class="form-group">
        <label for="nome_instituto_mae">Instituição</label>
        <input type="text" class="form-control" id="nome_instituto_mae" name="nome_instituto_mae" placeholder="Nome do Director da Instituição Mãe" value="{{old('nome_instituto_mae')}}">
      </div>  
      <div class="form-group">
        <label for="director_instituto_mae">Director</label>
        <input type="text" class="form-control" id="director_instituto_mae" name="director_instituto_mae" placeholder="Nome da Instituição Mãe" value="{{old('director_instituto_mae')}}">
      </div>        
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/cursos/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group">
        <label for="nome2">Nome</label>
        <input type="text" class="form-control" id="nome2" name="nome" v-model="$store.state.item.nome" placeholder="Nome">
      </div>
      <div class="form-group col-md-3">
        <label for="acronimo">Acrónimo</label>
        <input type="text" class="form-control" id="acronimo2" name="acronimo" v-model="$store.state.item.acronimo" placeholder="Acrónimo">
      </div>
      <div class="form-group col-md-5">
        <label for="coordenador">Coordenador</label>
        <select  class="form-control" id="coordenador" name="professor_id" v-model="$store.state.item.coordenador">
          @foreach($listaProfessores as $key => $value)              
              <option value="{{$value->nome}}">{{$value->nome}}</option>     
            @endforeach
        </select> 
      </div>
      <div class="form-group col-md-4">
        <label for="area">Área de Formação</label>
        <select  class="form-control" id="area" name="area" value="{{old('area')}}" v-model="$store.state.item.area">
            @foreach($listaAreas as $key => $value)              
              <option value="{{$value->nome}}">{{$value->nome}}</option>     
            @endforeach
        </select> 
      </div> 
      <div class="form-group">
        <label for="nome_instituto_mae">Instituição</label>
        <input type="text" class="form-control" id="nome_instituto_mae" name="nome_instituto_mae" placeholder="Nome da Instituição Mãe" v-model="$store.state.item.nome_instituto_mae">
      </div>  
      <div class="form-group">
        <label for="director_instituto_mae">Director</label>
        <input type="text" class="form-control" id="director_instituto_mae" name="director_instituto_mae" placeholder="Nome do Director da Instituição Mãe" v-model="$store.state.item.director_instituto_mae">
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
