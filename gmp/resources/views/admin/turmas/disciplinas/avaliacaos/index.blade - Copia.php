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

    <painel titulo="Avaliações da Turma {{$turma->nome}}">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>

      <tabela-lista
      v-bind:titulos="['#','Nº','Nome','Idade','F1','Mac','P1','P2','CT1','F2','Mac','P1','P2','CF2','CT1','CT2','F3','Mac','P1','CF3','CT2','CT3','MCT','60%','PG','40%','Med']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" editar="/admin/avaliacaos/"
      modal="sim"      
      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar Avaliação">
    <formulario id="formAdicionar" css="" action="/admin/turmas/{{$turma->id}}/disciplinas/{{$disciplina->id}}/avaliacaos" method="post" enctype="" token="{{csrf_token()}}">

      <input type="hidden" name="professor_id" value="{{$turma->id}}">
      <input type="hidden" name="user_id" value="">
      <input type="hidden" name="disciplina_id" value="{{$disciplina->id}}">
      <div class="form-group col-xs-12">
        <label for="nome">Nome</label>
        <select class="form-control" id="nome" name="aluno_id" value="{{old('aluno_id')}}">
         @foreach ($listaAlunosNaoAvaliados as $key => $value)
          <option value="{{$value->id}}">{{$value->nome}}</option>         
          @endforeach          
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
          <input type="text" class="form-control" id="fnj1" placeholder="Injustificadas" name="fnj1" value="{{old('fnj1')}}">
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
          <input type="text" class="form-control" id="fnj2" placeholder="Injustificadas" name="fnj2" value="{{old('fnj2')}}">
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
          <input type="text" class="form-control" id="fnj3" placeholder="Injustificadas" name="fnj3" value="{{old('fnj2')}}">
        </div>
        <div class="col-xs-3">
          <label for="fj3">Faltas</label>
          <input type="text" class="form-control" id="fj3" placeholder="Justificados" name="fj3" value="{{old('fj3')}}">
        </div>
      </div>     
    </formulario>
    <span slot="botoes">
      <a href="{{route('avaliacoes.upload')}}" class="btn btn-success"><i class="glyphicon glyphicon-file"></i>Importar De Excel</a>
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/avaliacaos/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <input type="hidden" name="professor_id" v-model=$store.state.item.professor_id>
      <input type="hidden" name="user_id" v-model=$store.state.item.user_id>
      <input type="hidden" name="disciplina_id" v-model=$store.state.item.disciplina_id>
      <input type="hidden" name="aluno_id" v-model=$store.state.item.aluno_id>      
      <div class="form-group col-xs-12">
        <div><h4>I Trimestre</h4></div>     
        <div class="col-xs-2">
          <label for="mac">Mac</label>
          <input type="text" class="form-control" id="mac" name="mac1" v-model=$store.state.item.mac1>
        </div>
        <div class="col-xs-2">
          <label for="p1">P1</label>
          <input type="text" class="form-control" id="p1" name="p11" v-model=$store.state.item.p11>
        </div>
        <div class="col-xs-2">
          <label for="p2">P2</label>
          <input type="text" class="form-control" id="p2" name="p12" v-model=$store.state.item.p12>
        </div>
        <div class="col-xs-3">
          <label for="fnj1">Faltas</label>
          <input type="text" class="form-control" id="fnj1" placeholder="Injustificadas" name="fnj1" v-model=$store.state.item.fnj1>
        </div>
        <div class="col-xs-3">
          <label for="fj1">Faltas</label>
          <input type="text" class="form-control" id="fj1" placeholder="Justificadas" name="fj1" v-model=$store.state.item.fj1>
        </div>
      </div>

      <div class="form-group col-xs-12">
        <div><h4>II Trimestre</h4></div>
        <div class="col-xs-2">
          <label for="mac">Mac</label>
          <input type="text" class="form-control" id="mac" name="mac2" v-model=$store.state.item.mac2>
        </div>
        <div class="col-xs-2">
          <label for="p1">P1</label>
          <input type="text" class="form-control" id="p1" name="p21" v-model=$store.state.item.p21>
        </div>
        <div class="col-xs-2">
          <label for="p2">P2</label>
          <input type="text" class="form-control" id="p2" name="p22" v-model=$store.state.item.p22>
        </div>
        <div class="col-xs-3">
          <label for="fnj2">Faltas</label>
          <input type="text" class="form-control" id="fnj2" placeholder="Injustificadas" name="fnj2" v-model=$store.state.item.fnj2>
        </div>
        <div class="col-xs-3">
          <label for="fj2">Faltas</label>
          <input type="text" class="form-control" id="fj2" placeholder="Justificados" name="fj2" v-model=$store.state.item.fj2>
        </div>
      </div>
       
       <div class="form-group col-xs-12">
        <div><h4>III Trimestre</h4></div>   
        <div class="col-xs-2">
          <label for="mac">Mac</label>
          <input type="text" class="form-control" id="mac" name="mac3" v-model=$store.state.item.mac3>
        </div>
        <div class="col-xs-2">
          <label for="p1">P1</label>
          <input type="text" class="form-control" id="p1" name="p31" v-model=$store.state.item.p31>
        </div>
        <div class="col-xs-2">
          <label for="p32">P2</label>
          <input type="text" class="form-control" id="p32" name="p32" v-model=$store.state.item.p32>
        </div>
        <div class="col-xs-3">
          <label for="fnj3">Faltas</label>
          <input type="text" class="form-control" id="fnj3" placeholder="Injustificadas" name="fnj3" v-model=$store.state.item.fnj3>
        </div>
        <div class="col-xs-3">
          <label for="fj3">Faltas</label>
          <input type="text" class="form-control" id="fj3" placeholder="Justificados" name="fj3" v-model=$store.state.item.fj3>
        </div>
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
