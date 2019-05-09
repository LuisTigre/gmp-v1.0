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

    <painel titulo="Lista de Anos Acadêmicos">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Trimestre','Ano Lectivo','Activo','Fechado?','Alterado por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="desc" ordemcol="2"
      criar="#criar"  editar="/admin/epocas/" 
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

      <!-- <div class="form-group">
        <label for="trimestre">Nome</label>
        <SELECT type="text" class="form-control" id="trimestre" name="trimestre" placeholder="Trimestre" value="{{old('trimestre')}}">
          <option value="I">I</option>
          <option value="II">II</option>
          <option value="III">III</option>
        </SELECT>
      </div> -->

      <div class="form-group">
        <label for="ano_lectivo">Ano Lectivo</label>
        <SELECT type="text" class="form-control" id="ano_lectivo" name="ano_lectivo" value="{{old('ano_lectivo')}}" value="{{old('ano_lectivo')}}">          
          @for($year = date('Y')+3;$year>=2009;$year--)
          <option value="{{$year}}">{{$year}}</option>
          @endfor          
        </SELECT>
      </div>     
      <!-- <div class="form-group">
        <label for="trimestre">Nome</label>
        <input type="text" class="form-control" id="trimestre" name="trimestre" placeholder="Trimestre" value="{{old('trimestre')}}">        
      </div> -->
       
    </formulario>
    <span slot="botoes">
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>

  </modal>
  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/epocas/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      
      <div class="form-group col-md-3">
        <label for="trimestre">Ano Lectivo</label>
        <input type="text" class="form-control" id="ano_lectivo" name="ano_lectivo" value="{{old('ano_lectivo')}}" v-model="$store.state.item.ano_lectivo" readonly>   
      </div>
      <div class="form-group col-md-3">
        <label for="trimestre">Trimestre</label>
        <SELECT type="text" class="form-control" id="trimestre" name="trimestre" placeholder="Trimestre" value="{{old('trimestre')}}" v-model="$store.state.item.trimestre">
          <option value="I">I</option>
          <option value="II">II</option>
          <option value="III">III</option>
        </SELECT>
      </div>          
      <div class="form-group col-md-3">
        <label for="activo">Status</label>
        <SELECT type="text" class="form-control" id="activo" name="activo"  v-model="$store.state.item.activo">         
          <option value="N">Desativo</option>                   
          <option value="S">Activo</option>
        </SELECT>
      </div>
      <div class="form-group col-md-3">
        <label for="fechado">Fechar?</label>
        <SELECT type="text" class="form-control" id="fechado" name="fechado"  v-model="$store.state.item.fechado">         
          <option value="N">Não</option>                   
          <option value="S">Sim</option>
        </SELECT>
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
