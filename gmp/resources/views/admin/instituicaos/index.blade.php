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

    <painel titulo="Instituição">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Sigla','Lema','Telefone','Email','Director','Pedagógico','Alt. Por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/instituicaos/" editar="/admin/instituicaos/" 
      deletar="/admin/instituicaos/" token="{{csrf_token()}}"
      modal="sim"
      
      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar Aluno">
    <formulario id="formAdicionar" css="" action="{{route('instituicaos.store')}}" method="post" enctype="" token="{{csrf_token()}}">      

      <div class="form-group col-md-12">
        <label for="nome">Nome:</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="sigla">Sigla:</label>
        <input type="text" class="form-control" id="sigla" name="sigla" placeholder="Sigla" value="{{old('sigla')}}">
      </div>
      <div class="form-group col-md-8">
        <label for="lema">Lema:</label>
        <input type="text" class="form-control" id="lema" name="lema" placeholder="Lema" value="{{old('lema')}}">
      </div>
      <div class="form-group col-md-2">
        <label for="numero">Número:</label>
        <input type="text" class="form-control" id="numero" name="numero" placeholder="Nº" value="{{old('numero')}}">
      </div>      
      <div class="form-group col-md-10">
        <label for="email"><i class="glyphicon glyphicon-envelope"></i>Email:</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{old('email')}}">
      </div>      
      <div class="form-group col-md-4">
        <label for="telefone1"><i class="glyphicon glyphicon-phone"></i>Telefone:</label>
        <input type="text" class="form-control" id="telefone1" name="telefone1" placeholder="Telefone" value="{{old('telefone1')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="telefone2"><i class="glyphicon glyphicon-phone"></i>Telefone:</label>
        <input type="text" class="form-control" id="telefone2" name="telefone2" placeholder="Telefone" value="{{old('telefone2')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="telefone3"><i class="glyphicon glyphicon-phone"></i>Telefone:</label>
        <input type="text" class="form-control" id="telefone3" name="telefone3" placeholder="Telefone" value="{{old('telefone3')}}">
      </div>
      <div class="form-group col-md-6">
        <label for="director"><i class="glyphicon glyphicon-user"></i>Director:</label>
        <input type="text" class="form-control" id="director" name="director_instituicao" placeholder="Director" value="{{old('director_instituicao')}}">
      </div>
      
      <div class="form-group col-md-6">
        <label for="pedagogico"><i class="glyphicon glyphicon-user"></i>Pedagógico:</label>
        <input type="text" class="form-control" id="pedagogico" name="director_pedagogico" placeholder="Pedagógico" value="{{old('director_pedagogico')}}">
      </div> 
      
        
     
           
    </formulario>
    <span slot="botoes">     
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>
  </modal>





  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/instituicaos/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <!-- <div class="form-group col-md-8">
        <label for="nome2">Nome</label>
        <input type="text" class="form-control" id="nome2" name="nome" v-model="$store.state.item.nome" placeholder="Nome">
      </div> -->
      
      <div class="form-group col-md-12">
        <label for="nome">Nome:</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" v-model="$store.state.item.nome">
      </div>
      <div class="form-group col-md-4">
        <label for="sigla">Sigla:</label>
        <input type="text" class="form-control" id="sigla" name="sigla" placeholder="Sigla" v-model="$store.state.item.sigla">
      </div>
      <div class="form-group col-md-8">
        <label for="lema">Lema:</label>
        <input type="text" class="form-control" id="lema" name="lema" placeholder="Lema" v-model="$store.state.item.lema">
      </div>
      <div class="form-group col-md-2">
        <label for="numero">Número:</label>
        <input type="text" class="form-control" id="numero" name="numero" placeholder="Nº" v-model="$store.state.item.numero">
      </div>
      <div class="form-group col-md-10">
        <label for="email"><i class="glyphicon glyphicon-envelope"></i>Email:</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Email" v-model="$store.state.item.email">
      </div>           
      <div class="form-group col-md-4">
        <label for="telefone1"><i class="glyphicon glyphicon-phone"></i>Telefone:</label>
        <input type="text" class="form-control" id="telefone1" name="telefone1" placeholder="Telefone" v-model="$store.state.item.telefone1">
      </div>
      <div class="form-group col-md-4">
        <label for="telefone2"><i class="glyphicon glyphicon-phone"></i>Telefone:</label>
        <input type="text" class="form-control" id="telefone2" name="telefone2" placeholder="Telefone" v-model="$store.state.item.telefone2">
      </div>
      <div class="form-group col-md-4">
        <label for="telefone3"><i class="glyphicon glyphicon-phone"></i>Telefone:</label>
        <input type="text" class="form-control" id="telefone3" name="telefone3" placeholder="Telefone" v-model="$store.state.item.telefone3">
      </div>
      <div class="form-group col-md-6">
        <label for="director"><i class="glyphicon glyphicon-user"></i>Director:</label>
        <input type="text" class="form-control" id="director" name="director_instituicao" placeholder="Director" v-model="$store.state.item.director_instituicao">
      </div>
      
      <div class="form-group col-md-6">
        <label for="pedagogico"><i class="glyphicon glyphicon-user"></i>Pedagógico:</label>
        <input type="text" class="form-control" id="pedagogico" name="director_pedagogico" placeholder="Pedagógico" v-model="$store.state.item.director_pedagogico">
      </div>              
      
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>
  
  <modal nome="detalhe" v-bind:titulo="$store.state.item.nome">
    <p><span class="labeled">Telefone 2:</span> @{{$store.state.item.telefone2}}</p></p>   
    <p><span class="labeled">Telefone 3:</span> @{{$store.state.item.telefone3}}</p>    
    <p><span class="labeled">Email:</span> @{{$store.state.item.email}}</p>
    <!-- <p><span class="labeled">Morada:</span> @{{$store.state.item.morada}}</p>
    <p><span class="labeled">Encar Tel:</span> @{{$store.state.item.encarregado_tel}}</p>   
    <p><span class="labeled">Pai:</span> @{{$store.state.item.pai}}</p>    
    <p><span class="labeled">Mãe:</span> @{{$store.state.item.mae}}</p>     -->
  </modal>
@endsection
   
   <style type="text/css">
        .labeled{
          font-weight: bold;
        }
   </style>
