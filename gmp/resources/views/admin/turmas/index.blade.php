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
    <painel titulo="Lista de Turmas">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
      @if($user->admin == 'S')     
      <tabela-lista
      v-bind:titulos="['#','Nome','Curso','Classe','Sala','Ano','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="desc" ordemcol="5"      
      criar="#criar" editar="/admin/turmas/" 
      deletar="/admin/turmas/" token="{{csrf_token()}}"
      modal="sim"
      v-bind:buttons="[
      {'nome':'Alunos','url':'/admin/turmas/' ,'action':'alunos'},
      {'nome':'Disciplinas','url':'/admin/turmas/','action':'disciplinas'},
      {'nome':'Pauta Trimestral','url':'/admin/turmas/','action':'pauta'},
      {'nome':'Pauta Final','url':'/admin/turmas/','action':'pautafinal'},
      {'nome':'Ficha de Notas','url':'/admin/turmas/','action':'ficha_apr'},
      {'nome':'Horário','url':'/admin/turmas/','action':'horario'},
      {'nome':'Aulas','url':'/admin/turmas/','action':'aulas'}
      ]"
      >
      <span class="no-print text-danger">          
          <a href="/admin/salas" class="btn btn-default btn-xs"></i>Salas</a>         
      </span> 
      </tabela-lista>
      @else
      <tabela-lista
      v-bind:titulos="['#','Nome','Curso','Classe','Sala','Ano','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="asc" ordemcol="1"     
      modal="sim"
      v-bind:buttons="[{'nome':'Alunos','url':'/admin/turmas/' ,'action':'alunos'},{'nome':'Pauta','url':'/admin/turmas/','action':'pauta'},{'nome':'Horário','url':'/admin/turmas/','action':'horario'},{'nome':'Disciplinas','url':'/admin/turmas/','action':'disciplinas'},{'nome':'Aulas','url':'/admin/turmas/','action':'aulas'}]"
      >
      @endif
      </tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="{{route('turmas.store')}}" method="post" enctype="" token="{{csrf_token()}}">      
      
      <div class="form-group">
        <label for="modulo">Modulo:</label>
        <select class="form-control" id="modulo" name="modulo">
            @foreach($listaModulos as $key => $value)              
              <option {{(old('modulo') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>

      <input type="hidden" name="user_id" value="{{$user->id}}">

      <input type="hidden" name="nome" value="{{old('nome')}}"> 

      <input type="hidden" name="ano_lectivo" value="{{$ano_lectivo}}">

      <div class="form-group">
        <label for="periodo">Periodo</label>        
        <select class="form-control" id="periodo" name="periodo">
          <option {{(old('periodo') && old('periodo') == 'Noturno' ? 'selected' : '') }} {{(!old('periodo') ? 'selected' : '')}} value="Noturno">Noturno</option>
          <option {{(old('periodo') && old('periodo') == 'Vespertino' ? 'selected' : '') }} {{(!old('periodo') ? 'selected' : '')}} value="Vespertino">Vespertino</option>
          <option {{(old('periodo') && old('periodo') == 'Matinal' ? 'selected' : '') }} value="Matinal">Matinal</option>
        </select>
      </div>
      <!-- <div class="form-group">
        <label for="periodo">Ano Lectivo</label>        
        <select class="form-control" id="ano" name="ano">
          <option {{(old('ano') && old('ano') == 'Matinal' ? 'selected' : '') }} value="Matinal">Matinal</option>
          <option {{(old('ano_lectivo') && old('ano_lectivo') == 'Vespertino' ? 'selected' : '') }} {{(!old('ano_lectivo') ? 'selected' : '')}} value="Vespertino">Vespertino</option>
          <option {{(old('ano_lectivo') && old('ano_lectivo') == 'Noturno' ? 'selected' : '') }} {{(!old('ano_lectivo') ? 'selected' : '')}} value="Noturno">Noturno</option>
        </select>
      </div> -->

      <div class="form-group">
        <label for="sala">Salas:</label>
        <select class="form-control" id="sala" name="sala_id">
            @foreach($salas as $key => $value)              
              <option {{(old('sala') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div> 

      <!-- <div class="form-group">
        <label for="ano_lectivo">Ano lectivo:</label>
        <select class="form-control" id="ano_lectivo" name="ano_lectivo">           
            @for ($i=$ano_lectivo; $i >= 2000; $i--)
              <option {{(old('ano_lectivo') ? 'selected' : '') }} value="$i">{{$i}}</option>     
            @endfor
        </select>
      </div> -->      
      

    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/turmas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">
      
      <input type="hidden" name="id" v-model="$store.state.item.id">      
      
      <div class="form-group">
        <label for="periodo">Periodo:</label>
        <select class="form-control" id="periodo" name="periodo" v-model="$store.state.item.periodo">
          <option value="Matinal">Matinal</option>
          <option value="Vespertino">Vespertino</option>
          <option value="Noturno">Noturno</option>
        </select>
      </div>

      <div class="form-group">
        <label for="sala">Salas:</label>
        <select class="form-control" id="sala" name="sala_id" v-model="$store.state.item.sala_id">
            @foreach($salas as $key => $value)              
              <option {{(old('sala') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>          
      <input type="hidden" name="ano_lectivo" value="{{$ano_lectivo}}">
      <input type="hidden" name="user_id" value="{{$user->id}}">
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>Nome: <span>@{{$store.state.item.nome}}</span></p>
    <p>Perido: <span>@{{$store.state.item.periodo}}</span></p>         
    <span>
      <a v-bind:href="'/admin/turmas/'+$store.state.item.id+'/disciplinas'" data-toggle="tooltip" data-placement="bottom" title="Clique aquí para adicionar mais professores a turma">Disciplinas</a>

      <a v-bind:href="'/admin/turmas/'+$store.state.item.id+'/alunos'" data-toggle="tooltip" data-placement="bottom" title="Clique aquí para adicionar mais alunos a turma">Alunos</a>

      <a v-bind:href="'/admin/turmas/'+$store.state.item.id+'/pauta'" data-toggle="tooltip" data-placement="bottom" title="Clique aquí para ver a mini pauta da turma">Pauta</a>
      <a v-bind:href="'/admin/turmas/'+$store.state.item.id+'/pautaPDF'" data-toggle="tooltip" data-placement="bottom" title="Clique aquí para imprimir a mini pauta em PDF">Pauta PDF</a>
    </span>
  </modal>
@endsection
