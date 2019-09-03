@extends('layouts.app')
     <!--  v-bind:deletar="'/admin/apagar/professores/'+{{json_encode($professor->id)}}+'/turmas/'"  -->

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

    <painel titulo="{{$professor->nome}} / Turmas">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>   
    
      <tabela-lista
      v-bind:titulos="['#','Turma','Discplina','Ano','Director']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      criar="#" 
      editar="itself/admin/turmas/{{{json_encode($turma->id)}}}/disciplinas/"
      token="{{csrf_token()}}"
      ordem="desc" ordemcol="3"      
      modal="sim"
      
      ></tabela-lista>
     
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="/admin/turmas/{{{json_encode($turma->id)}}}/disciplinas" method="post" enctype="" token="{{csrf_token()}}">

      <input type="hidden" name="professor_id" value="{{$professor->id}}">      
      <div class="form-group col-md-8">
        <label for="turma">Turma:</label>
        <select class="form-control" id="turma" name="turma_id">
            @foreach($listaTurmas as $key => $value)              
              <option {{(old('turma') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>
      <div class="form-group col-md-4">
        <label for="director">director:</label>
        <select class="form-control" id="director" name="director">                      
          <option {{(old('director') && old('director') == 'N' ? 'selected' : '') }} value="N">Não</option>
          <option {{(old('director') && old('director') == 'S' ? 'selected' : '') }} value="S">Sim</option>  
        </select>
      </div> 
      <div class="form-group  col-md-12">
        <label for="disciplina">Disciplina:</label>
        <select class="form-control" id="disciplina" name="disciplina_id">
            @foreach($listaDisciplinas as $key => $value)              
              <option {{(old('disciplina') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>
    </formulario>
    <span slot="botoes">      
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Deletar">
    <formulario id="formEditar" v-bind:action="'/admin/turmas/'+$store.state.item.id+'/disciplinas/{{json_encode($professor->id)}}'" method="delete" enctype="multipart/form-data" token="{{csrf_token()}}">

     
      <input type="hidden" value="" name="user_id"> 
       <input type="hidden" name="disciplina_id" value="">
       <input type="hidden" name="turma_id" v-model="$store.state.item.id">
      <input type="hidden" name="professor" value="{{$professor->nome}}"> 

      <div class="form-group col-md-4">
        <label for="turma">Turma:</label>
        <input type="text" class="form-control" id="turma" readonly v-model="$store.state.item.turma">
      </div>

      <!-- <div class="form-group col-md-4">
        <label for="director">director:</label>
        <select class="form-control" id="director" name="director" v-model="$store.state.item.director">                      
          <option value="N">Não</option>
          <option value="S">Sim</option>  
        </select>
      </div>  -->
      <div class="form-group  col-md-8">
        <label for="disciplina">Disciplina:</label>
        <input class="form-control" id="disciplina" name="disciplina" readonly v-model="$store.state.item.disciplina">         
      </div>     
        
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Limpar Tudo</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.email}}</p>
  </modal>
@endsection
