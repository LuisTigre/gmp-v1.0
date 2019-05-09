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

    <painel v-bind:titulo="'Lista de disciplinas de '+ {{json_encode($modulo->nome)}}">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
      <tabela-lista
      v-bind:titulos="['#','Disciplina','Acrónim','Carga Horária','Terminal','Do Curso?','Curricular','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"       
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/modulos/disciplinas/" editar="itself/admin/modulos/disciplinas/" 
      v-bind:deletar="'/admin/modulos/'+{{json_encode($modulo->id)}}+'/disciplinas/'" token="{{csrf_token()}}"
      modal="sim"
 
      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar">
    <formulario id="formAdicionar" css="" action="/admin/modulos/{{{json_encode($modulo->id)}}}/disciplinas" method="post" enctype="" token="{{csrf_token()}}">

       <input type="hidden" value="{{$modulo->id}}" name="modulo_id">    
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

      <!-- <div class="form-group">
        <label for="carga">Carga Horária:</label>
        <input class="form-control" id="carga" name="carga" value="{{old('carga')}}"> 
      </div>
      
      <div class="form-group">
        <label for="terminal">Terminal</label>        
        <select class="form-control" id="terminal" name="terminal">
          <option {{(old('terminal') && old('terminal') == 'S' ? 'selected' : '') }} value="S">S</option>
          <option {{(old('terminal') && old('terminal') == 'N' ? 'selected' : '') }} {{(!old('terminal') ? 'selected' : '')}} value="N">N</option>          
        </select>
      </div>
      <div class="form-group">
        <label for="do_curso">Do Curso?</label>        
        <select class="form-control" id="do_curso" name="do_curso">
          <option {{(old('do_curso') && old('do_curso') == 'S' ? 'selected' : '') }} value="S">S</option>
          <option {{(old('do_curso') && old('do_curso') == 'N' ? 'selected' : '') }} {{(!old('do_curso') ? 'selected' : '')}} value="N">N</option>          
        </select>
      </div> -->

      

    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/modulos/{{$modulo->id}}/disciplinas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">
      
      <input type="hidden" name="modulo_id" v-bind:value="{{json_encode($modulo->id)}}">

      <input type="hidden" name="disciplina_id" v-model="$store.state.item.id">

      <div class="form-group">
        <label for="disciplina">Disciplinas:</label>
        <input class="form-control" id="disciplina" readonly name="disciplina" v-model="$store.state.item.disciplina"> 
      </div>
      <div class="form-group col-md-6">
        <label for="terminal">Terminal</label>        
        <select class="form-control" id="terminal" name="terminal" v-model="$store.state.item.terminal">          
          <option value="S">S</option>
          <option value="N">N</option>          
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="Curricular">Curricular</label>        
        <select class="form-control" id="curricular" name="curricular" v-model="$store.state.item.curricular">          
          <option value="S">S</option>
          <option value="N">N</option>          
        </select>
      </div>               

      <div class="form-group col-md-6">
        <label for="carga">Carga Horária:</label>
        <input class="form-control" id="carga" name="carga" v-model="$store.state.item.carga">            
      </div>
      <div class="form-group col-md-6">
        <label for="do_curso">Do Curso ?</label>        
        <select class="form-control" id="do_curso" name="do_curso" v-model="$store.state.item.do_curso">          
          <option value="S">S</option>
          <option value="N">N</option>          
        </select>
      </div>

      <input type="hidden" name="user_id" value="{{$user->id}}">
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  <modal nome="detalhe" v-bind:titulo="$store.state.item.name">    
    <p>@{{$store.state.item.disciplina}}</p>
    <p> @{{$store.state.item.duracao}}    
    <p> @{{$store.state.item.carga}}    
  </modal>
@endsection
