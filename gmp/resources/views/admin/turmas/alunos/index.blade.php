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

    <painel v-bind:titulo="'Lista dos alunos da turma '+ {{json_encode($turma->nome)}}">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
      @if($user->admin=='S')
      <tabela-lista
      v-bind:titulos="['#','Nº','Nome Completo','Devedor','Status','Cargo','Alt. por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/turmas/{{{json_encode($turma->id)}}}/alunos/" 
      editar="/admin/turmas/{{{json_encode($turma->id)}}}/alunos/" 
      v-bind:deletar="'/admin/turmas/'+{{json_encode($turma->id)}}+'/alunos/'" token="{{csrf_token()}}"
      modal="sim" 
      ></tabela-lista>
      @else
      <tabela-lista
      v-bind:titulos="['#','Nº','Nome Completo','Status','Cargo','Alt. por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="asc" ordemcol="1"
      detalhe="/admin/turmas/alunos/" token="{{csrf_token()}}"
      modal="sim" 
      ></tabela-lista>
      @endcan

      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
      <formulario id="formAdicionar" css="" action="/admin/turmas/{{{json_encode($turma->id)}}}/alunos" method="post" enctype="multipart/form-data" files="true" token="{{csrf_token()}}">

    <input type="hidden" value="{{$turma->id}}" name="turma_id">    
      <div class="form-group">
        <label for="aluno">Alunos:</label>
        <select class="form-control" id="aluno" name="aluno_id[]" multiple size="20">
            @foreach($listaAlunos as $key => $value)              
              <option {{(old('disciplina') ? 'selected' : '') }} value="{{$value->id}}">{{$value->data_de_nascimento}} ----  {{$value->nome}}</option>     
            @endforeach
        </select>
    </div>
    <input type="hidden" value="" name="numero">    
    <input type="hidden" value="" name="cargo">    
    <input type="hidden" value="" name="user_id">    


    </formulario>
    <span slot="botoes">
      <a form="formAdicionar" href="/admin/alunos/" class="btn btn-info bnt-sm">Novo</a>
      <button form="formAdicionar" class="btn btn-info bnt-sm">Adicionar</button>
    </span>

  </modal>

  

  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/turmas/'+{{json_encode($turma->id)}}+'/alunos/'+$store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">
      
      <input type="hidden" name="turma_id" v-bind:value="{{json_encode($turma->id)}}">

      <input type="hidden" name="aluno_id" v-model="$store.state.item.id">

      <div class="form-group col-md-12">
        <label for="aluno">Aluno:</label>
        <input class="form-control" id="aluno" readonly name="nome" v-model="$store.state.item.nome"> 
      </div>
    
      <div class="form-group col-md-6">
        <label for="cargo">Cargo</label>        
        <select class="form-control" id="cargo" name="cargo" v-model="$store.state.item.cargo">          
          <option value="Delegado"></option>
          <option value="Delegado">Delegado</option>
          <option value="Sub Delegado">Sub Delegado</option>
          <option value="Chefe de Limpeza">Chefe de Limpeza</option>
          <option value="Concelheiro">Concelheiro</option>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="status">Status</label>        
        <select class="form-control" id="status" name="status" v-model="$store.state.item.status">          
          <option value="Activo">Activo</option>
          <option value="Transferido">Transferido</option>
          <option value="Suspenso">Suspenso</option>
          <option value="Desistido">Desistido</option>
        </select>
      </div>     
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.nome">
    <p><span class="labeled">Numero Mat:</span> @{{$store.state.item.idmatricula}}</p></p>   
    <p><span class="labeled">Telefone:</span> @{{$store.state.item.telefone}}</p>    
    <p><span class="labeled">Email:</span> @{{$store.state.item.email}}</p>
    <p><span class="labeled">Morada:</span> @{{$store.state.item.morada}}</p>
    <p><span class="labeled">Encar Tel:</span> @{{$store.state.item.encarregado_tel}}</p>   
    <p><span class="labeled">Pai:</span> @{{$store.state.item.pai}}</p>    
    <p><span class="labeled">Mãe:</span> @{{$store.state.item.mae}}</p>    
  </modal>
@endsection
   
   <style type="text/css">
        .labeled{
          font-weight: bold;
        }

        
   </style>
