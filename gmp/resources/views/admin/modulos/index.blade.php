@extends('layouts.app')

@section('content')
  <pagina tamanho="8">
    @if($errors->all())
      <div class="alert alert-danger alert-dismissible text-center" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        @foreach ($errors->all() as $key => $value)
          <li><strong>{{$value}}</strong></li>
        @endforeach
      </div>
    @endif        
    <painel titulo="Lista de Modulos">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>      
      <tabela-lista
      v-bind:titulos="['#','Nome','Ano','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="asc" ordemcol="1" criar="#criar"
      deletar="/admin/modulos/" token="{{csrf_token()}}"
      modal="sim"
      v-bind:buttons="[{'nome':'Disciplinas','url':'/admin/modulos/' ,'action':'disciplinas'}]"

      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('modulos.store')}}" method="post" enctype="" token="{{csrf_token()}}">

      
      <input type="hidden" name="nome" value="{{old('nome')}}">      
      <div class="form-group">
        <label for="curso">Cursos:</label>
        <select class="form-control" id="curso" name="curso">
            @foreach($listaCursos as $key => $value)              
              <option {{(old('curso') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>  
      <div class="form-group">
        <label for="classe">Classes:</label>
        <select class="form-control" id="classe" name="classe">
            @foreach($listaClasses as $key => $value)              
              <option {{(old('classe') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>           
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/modulo/' + $store.state.item.id + '/disciplina/'" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">
      
      <input type="hidden" name="nome" v-model="$store.state.item.nome">      
      <div class="form-group">
        <label for="curso">Cursos:</label>
        <select class="form-control" id="curso" name="curso" v-model="$store.state.item.curso">
            @foreach($listaCursos as $key => $value)              
              <option value="{{$value->nome}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>  
      <div class="form-group">
        <label for="classe">Classes:</label>
        <select class="form-control" id="classe" name="classe" v-model="$store.state.item.classe">
            @foreach($listaClasses as $key => $value)              
              <option value="{{$value->nome}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>               
      <input type="hidden" name="user_id" value="{{$user->id}}">
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.curso_id}}</p>
    <p> @{{$store.state.item.name}}      
    <span><a v-bind:href="'/admin/modulos/'+$store.state.item.id+'/disciplinas'" data-toggle="tooltip" data-placement="bottom" title="Clique aquí para adicionar mais disciplinas ao Módulo">Quantidade de disciplinas:</a></span></p>
  </modal>
@endsection
