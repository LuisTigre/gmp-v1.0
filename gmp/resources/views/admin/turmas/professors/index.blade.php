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

    <painel v-bind:titulo="'Lista nominal dos Professores da turma '+ {{json_encode($turma->nome)}}">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
      <tabela-lista
      v-bind:titulos="['#','Disciplina','Professor','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/turmas/professors/" 
      editar="'/admin/turmas/'+{{json_encode($turma->id)}}+'/professors/" 
      v-bind:deletar="'/admin/turmas/'+{{json_encode($turma->id)}}+'/professors/'" token="{{csrf_token()}}"
      modal="sim"
 
      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
      <formulario id="formAdicionar" css="" action="/admin/turmas/{{{json_encode($turma->id)}}}/professors" method="post" enctype="" token="{{csrf_token()}}">

    <input type="hidden" value="{{$turma->id}}" name="turma_id">    
      <div class="form-group">
        <label for="disciplina">Disciplina:</label>
        <select class="form-control" id="disciplina" name="disciplina_id">
            @foreach($listadisciplinas  as $key => $value)              
              <option {{(old('disciplina_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="professor">Professor:</label>
        <select class="form-control" id="professor" name="professor_id">
            @foreach($listaProfessores as $key => $value)              
              <option {{(old('professor_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>
    <div class="form-group">
        <label for="director">Director</label>        
        <select class="form-control" id="director" name="director">
          <option {{(old('director') && old('director') == 'S' ? 'selected' : '') }} value="S">Sim</option>
          <option {{(old('director') && old('director') == 'N' ? 'selected' : '') }} {{(!old('director') ? 'selected' : '')}} value="N">Não</option>
        </select>
      </div>        
    <input type="hidden" value="" name="user_id">    


    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/turmas/'+{{json_encode($turma->id)}}+'/professors/'+$store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">
      
      <input type="hidden" value="{{$turma->id}}" name="turma_id">    
      <div class="form-group">
        <label for="disciplina">Disciplina:</label>
        <input type="text" class="form-control" id="disciplina" name="disciplina_id" readonly v-model="$store.state.item.disciplina">            
      </div>
      <div class="form-group">
        <label for="professor">Professor:</label>
        <select class="form-control" id="professor" name="professor" v-model="$store.state.item.professor">
            @foreach($listaProfessores as $key => $value)              
              <option {{(old('professor_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>

      <input type="hidden" value="" name="user_id"> 

    <div class="form-group">
        <label for="director">Director</label>        
        <select class="form-control" id="director" name="director" v-model="$store.state.item.director">
          <option value='S'>Sim</option>
          <option value='N'>Não</option>         
        </select>
      </div>        



    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">
    <p>@{{$store.state.item.nome}}</p>
    <p> @{{$store.state.item.numero}}    
    <p> @{{$store.state.item.cargo}}    
  </modal>
@endsection
