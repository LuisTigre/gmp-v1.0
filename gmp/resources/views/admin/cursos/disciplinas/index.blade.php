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

    <painel titulo="{{$curso->nome}} / Disciplinas">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Disciplina','Acron.','10ª','11ª','12ª','13ª','Curricular','Categoria']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="8"
      criar="#criar" editar="itself/admin/cursos/"      
      modal="sim"      
      >
       <span class="no-print text-danger">  
          <a href="/admin/disciplinas" class="btn btn-default btn-xs"></i>Disciplinas</a>
       </span>  
      </tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="/admin/modulos/{{{json_encode($modulo->id)}}}/disciplinas" method="post" enctype="" token="{{csrf_token()}}">
       <input type="hidden" name="modulo_id" v-bind:value="{{json_encode($modulo->id)}}">
       <input type="hidden" value="2" name="carga">    
       <input type="hidden" value="N" name="terminal">    
       <input type="hidden" value="N" name="do_curso">

      <div class="form-group">
        <label for="disciplina">Disciplinas:</label>
        <select class="form-control" id="disciplina" name="disciplina_id[]" multiple size="20">
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
  <modal nome="editar" titulo="Editar">

    <formulario id="formEditar" v-bind:action="'/admin/modulos/{{$modulo->id}}/disciplinas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <input type="hidden" name="modulo_id" v-bind:value="{{json_encode($modulo->id)}}">
      <input type="hidden" name="curso_id" v-bind:value="{{json_encode($curso->id)}}">

      <input type="hidden" name="disciplina_id" v-model="$store.state.item.id">      

      <div class="form-group col-md-10">
        <label for="disciplina">Disciplinas:</label>
        <input class="form-control" id="disciplina" readonly name="disciplina" v-model="$store.state.item.disciplina"> 
      </div>
      <!-- <div class="form-group col-md-6">
        <label for="terminal">Terminal</label>        
        <select class="form-control" id="terminal" name="terminal" v-model="$store.state.item.terminal">          
          <option value="S">S</option>
          <option value="N">N</option>          
        </select>
      </div> -->
      <div class="form-group col-md-2">
        <label for="Curricular">Curricular</label>        
        <select class="form-control" id="curricular" name="curricular" v-model="$store.state.item.curricular">          
          <option value="S">S</option>
          <option value="N">N</option>          
        </select>
      </div>               
      
      <div class="form-group col-md-3">
        <label for="carga">Carga H. 10ª:</label>
        <select class="form-control" id="carga_10" name="carga_10" v-model="$store.state.item.carga_10">
          <option value=""></option>                    
        @for($valor = 1; $valor <= 6; $valor++)
          <option value="{{$valor}}">{{$valor}}</option>                    
        @endfor             
        </select>
      </div> 
      <div class="form-group col-md-3">
        <label for="carga_11">Carga H. 11ª:</label>
        <select class="form-control" id="carga_10" name="carga_11" v-model="$store.state.item.carga_11">
          <option value=""></option>
        @for($valor = 1; $valor <= 6; $valor++)
          <option value="{{$valor}}">{{$valor}}</option>                    
        @endfor             
        </select>            
      </div>
      <div class="form-group col-md-3">
        <label for="carga_12">Carga H. 12ª:</label>
        <select class="form-control" id="carga_10" name="carga_12" v-model="$store.state.item.carga_12">
          <option value=""></option>
        @for($valor = 1; $valor <= 6; $valor++)
          <option value="{{$valor}}">{{$valor}}</option>                    
        @endfor             
        </select>            
      </div>
      <div class="form-group col-md-3">
        <label for="carga_13">Carga H. 13ª:</label>
        <select class="form-control" id="carga_10" name="carga_13" v-model="$store.state.item.carga_13">
        <option value=""></option>
        @for($valor = 1; $valor <= 6; $valor++)
          <option value="{{$valor}}">{{$valor}}</option>                    
        @endfor             
        </select>            
      </div>
      <!-- <div class="form-group col-md-3">
        <label for="do_curso">Do Curso ?</label>        
        <select class="form-control" id="do_curso" name="do_curso" v-model="$store.state.item.do_curso">          
          <option value="S">S</option>
          <option value="N">N</option>          
        </select>
      </div> -->

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
