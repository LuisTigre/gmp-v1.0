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

    <painel titulo="Lista de Alunos">
      <migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
     
      <tabela-lista
      v-bind:titulos="['#','Nome','Data de Nasc','Nº Da Matricula','Modulo','Gênero','Status','Alt. Por']"
      v-bind:itens="{{json_encode($listaModelo)}}"
      ordem="asc" ordemcol="1"
      criar="#criar" detalhe="/admin/alunos/" editar="/admin/alunos/" 
      deletar="/admin/alunos/" token="{{csrf_token()}}"
      modal="sim"
      v-bind:buttons="[{'nome':'Bolentim','url':'/admin/alunos/' ,'action':'bolentim'},{'nome':'Declaração','url':'/admin/alunos/' ,'action':'dec_com_notas'}]"
      ></tabela-lista>
      <div align="center">        
        {{$listaModelo}}
      </div>
    </painel>

  </pagina>

  <modal nome="adicionar" titulo="Adicionar Aluno">
    <formulario id="formAdicionar" css="" action="{{route('alunos.store')}}" method="post" enctype="" token="{{csrf_token()}}">      

      <div class="form-group col-md-8">
        <label for="nome"><i class="glyphicon glyphicon-user"></i>Nome:</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" value="{{old('nome')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="ddn"><i class="glyphicon glyphicon-calendar"></i>Nascimento:</label>
        <input type="date" class="form-control" id="ddn" name="data_de_nascimento" placeholder="Data de Nascimento" value="{{old('data_de_nascimento')}}">
      </div>    
      <div class="form-group col-md-4">
        <label for="idmatricula">Nº da Matrícula:</label>
        <input type="text" class="form-control" id="idmatricula" name="idmatricula" placeholder="Nº de matrícula" value="{{old('idmatricula')}}">
      </div>
      <div class="form-group col-md-3">
        <label for="modulo">Modulo:</label>
        <select class="form-control" id="modulo" name="modulo_id">
            @foreach($listaModulos as $key => $value)              
              <option {{(old('modulo_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>        
      <div class="form-group col-md-2">
        <label for="repetente">Sexo:</label>        
        <select class="form-control" id="sexo" name="sexo">
          <option {{(old('sexo') && old('Sexo') == 'F' ? 'selected' : '') }} value="F">F</option>
          <option {{(old('sexo') && old('sexo') == 'M' ? 'selected' : '') }} {{(!old('sexo') ? 'selected' : '')}} value="M">M</option>
        </select>
      </div>  
      <div class="form-group col-md-3">
        <label for="repetente">Repetente</label>        
        <select class="form-control" id="repetente" name="repetente">
          <option {{(old('repetente') && old('repetente') == 'N' ? 'selected' : '') }} value="N">Não</option>
          <option {{(old('repetente') && old('repetente') == 'S' ? 'selected' : '') }} {{(!old('repetente') ? 'selected' : '')}} value="S">Sim</option>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="pai"><i class="glyphicon glyphicon-user"></i>Pai:</label>
        <input type="text" class="form-control" id="pai" name="pai" placeholder="pai" value="{{old('pai')}}">
      </div>    
      <div class="form-group col-md-6">
        <label for="mae"><i class="glyphicon glyphicon-user"></i>Mãe:</label>
        <input type="text" class="form-control" id="mae" name="mae" placeholder="mae" value="{{old('mae')}}">
      </div>
      <div class="form-group col-md-3">
        <label for="doctipo">Tipo</label>        
        <select class="form-control" id="doctipo" name="doctipo">
          <option {{(old('doctipo') && old('doctipo') == 'Passaporte' ? 'selected' : '') }} value="BI">BI</option>
          <option {{(old('doctipo') && old('doctipo') == 'Passaporte' ? 'selected' : '') }} {{(!old('doctipo') ? 'selected' : '')}} value="Passaporte">Passaporte</option>
        </select>
      </div>
      <div class="form-group col-md-5">
        <label for="doc_numero"><i class=""></i> Número Doc</label>
        <input type="text" class="form-control" id="doc_numero" name="doc_numero" placeholder="Número do documento" value="{{old('doc_numero')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="doc_local_emissao"><i class=""></i>Local</label>
        <input type="text" class="form-control" id="doc_local_emissao" name="doc_local_emissao" placeholder="Local de Emissão" value="{{old('doc_local_emissao')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="doc_data_emissao"><i class="glyphicon glyphicon-calendar"></i>Data de Emissão:</label>
        <input type="date" class="form-control" id="doc_data_emissao" name="doc_data_emissao" placeholder="Data de Emissão" value="{{old('doc_data_emissao')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="doc_data_validade"><i class="glyphicon glyphicon-calendar"></i>Validade:</label>
        <input type="date" class="form-control" id="doc_data_validade" name="doc_data_validade" placeholder="Data de Nascimento" value="{{old('doc_data_validade')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="provincia"><i class=""></i>Província</label>
        <input type="text" class="form-control" id="provincia" name="provincia" placeholder="provincia" value="{{old('provincia')}}">
      </div>
      <div class="form-group col-md-4">
        <label for="pais"><i class=""></i>País</label>
        <input type="text" class="form-control" id="pais" name="pais" placeholder="pais" value="{{old('pais')}}">
      </div>
      <div class="form-group col-md-8">
        <label for="morada"><i class=""></i>Morada</label>
        <input type="text" class="form-control" id="morada" name="morada" placeholder="morada" value="{{old('morada')}}">
      </div>
      <div class="form-group col-md-6">
        <label for="escola_origem"><i class=""></i>Escola de origem</label>
        <input type="text" class="form-control" id="escola_origem" name="escola_origem" placeholder="Escola de origem" value="{{old('escola_origem')}}">
      </div>
      <div class="form-group col-md-6">
        <label for="naturalidade"><i class=""></i>Naturalidade</label>
        <input type="text" class="form-control" id="naturalidade" name="naturalidade" placeholder="naturalidade" value="{{old('naturalidade')}}">
      </div>       
      <div class="form-group col-md-3">
        <label for="telefone"><i class="glyphicon glyphicon-phone"></i>Telefone</label>
        <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone" value="{{old('telefone')}}">
      </div>
      <div class="form-group col-md-3">
        <label for="encarregado"><i class="glyphicon glyphicon-phone"></i>Tel. Encar:</label>
        <input type="text" class="form-control" id="encarregado" name="encarregado_tel" value="{{old('encarregado_tel')}}" placeholder="Tel. do Encaregado">
      </div>
      <div class="form-group col-md-6">
        <label for="email"><i class="glyphicon glyphicon-envelope"></i> E-mail</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="{{old('email')}}">
      </div> 
      <div class="form-group col-md-12">
        <label for="descricao"><i class="glyphicon glyphicon-comment"></i>descricao</label>
        <input type="text" class="form-control" id="descricao" name="descricao" placeholder="descricao" value="{{old('descricao')}}">
      </div>     
    </formulario>
    <span slot="botoes">
      <a href="{{route('alunos.upload')}}" class="btn btn-success"><i class="glyphicon glyphicon-file"></i>Importar de Excel</a>
      <button form="formAdicionar" class="btn btn-info">Adicionar</button>
    </span>
  </modal>





  <modal nome="editar" titulo="Editar">
    <formulario id="formEditar" v-bind:action="'/admin/alunos/' + $store.state.item.id" method="put" enctype="multipart/form-data" token="{{csrf_token()}}">

      <div class="form-group col-md-8">
        <label for="nome2">Nome</label>
        <input type="text" class="form-control" id="nome2" name="nome" v-model="$store.state.item.nome" placeholder="Nome">
      </div>
      <div class="form-group col-md-4">
        <label for="idmatricula">Nº da Matrícula</label>
        <input type="text" class="form-control" id="idmatricula" name="idmatricula" v-model="$store.state.item.idmatricula" placeholder="Nº de matrícula">
      </div> 
      <div class="form-group col-md-4">
        <label for="ddn">Data de Nascimento</label>
        <input type="date" class="form-control" id="ddn" name="data_de_nascimento" placeholder="Data de Nascimento" v-model="$store.state.item.data_de_nascimento">
      </div>
      <div class="form-group col-md-3">
        <label for="modulo">Modulo:</label>
        <select class="form-control" id="modulo" name="modulo_id" v-model="$store.state.item.modulo_id">
            @foreach($listaModulos as $key => $value)              
              <option {{(old('modulo_id') ? 'selected' : '') }} value="{{$value->id}}">{{$value->nome}}</option>     
            @endforeach
        </select>
      </div>
      <div class="form-group col-md-2">
        <label for="sexo">Sexo:</label>        
        <select class="form-control" id="sexo" name="sexo" v-model="$store.state.item.sexo">
          <option value="F">F</option>
          <option value="M">M</option>
        </select>
      </div>                  
      <div class="form-group col-md-3">
        <label for="repetente2">Repetente</label>        
        <select class="form-control" id="repetente2" name="repetente" v-model="$store.state.item.repetente">
          <option value="N">Não</option>
          <option value="S">Sim</option>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="pai"><i class="glyphicon glyphicon-user"></i>Pai:</label>
        <input type="text" class="form-control" id="pai" name="pai" placeholder="pai" v-model="$store.state.item.pai">
      </div>    
      <div class="form-group col-md-6">
        <label for="mae"><i class="glyphicon glyphicon-user"></i>Mãe:</label>
        <input type="text" class="form-control" id="mae" name="mae" placeholder="mae" v-model="$store.state.item.mae">
      </div>
      <div class="form-group col-md-3">
        <label for="doctipo">Tipo</label>        
        <select class="form-control" id="doctipo" name="doctipo" v-model="$store.state.item.doctipo">
          <option value="BI">BI</option>
          <option value="Passaporte">Passaporte</option>
        </select>
      </div>
      <div class="form-group col-md-5">
        <label for="doc_numero"><i class=""></i> Número Doc</label>
        <input type="text" class="form-control" id="doc_numero" name="doc_numero" placeholder="doc_numero" v-model="$store.state.item.doc_numero">
      </div>
      <div class="form-group col-md-4">
        <label for="doc_local_emissao"><i class=""></i>Local</label>
        <input type="text" class="form-control" id="doc_local_emissao" name="doc_local_emissao" placeholder="Local de Emissão" v-model="$store.state.item.doc_local_emissao">
      </div>
      <div class="form-group col-md-4">
        <label for="doc_data_emissao"><i class="glyphicon glyphicon-calendar"></i>Data de Emissão:</label>
        <input type="date" class="form-control" id="doc_data_emissao" name="doc_data_emissao" placeholder="Data de Emissão" v-model="$store.state.item.doc_data_emissao">
      </div>
      <div class="form-group col-md-4">
        <label for="doc_data_validade"><i class="glyphicon glyphicon-calendar"></i>Validade:</label>
        <input type="date" class="form-control" id="doc_data_validade" name="doc_data_validade" placeholder="Data de Nascimento" v-model="$store.state.item.doc_data_validade">
      </div>
      <div class="form-group col-md-4">
        <label for="provincia"><i class=""></i>Província</label>
        <input type="text" class="form-control" id="provincia" name="provincia" placeholder="provincia" v-model="$store.state.item.provincia">
      </div>
      <div class="form-group col-md-4">
        <label for="pais"><i class=""></i>País</label>
        <input type="text" class="form-control" id="pais" name="pais" placeholder="pais" v-model="$store.state.item.pais">
      </div>
      <div class="form-group col-md-8">
        <label for="morada"><i class=""></i>Morada</label>
        <input type="text" class="form-control" id="morada" name="morada" placeholder="morada" v-model="$store.state.item.morada">
      </div>
      <div class="form-group col-md-6">
        <label for="escola_origem"><i class=""></i>Escola de origem</label>
        <input type="text" class="form-control" id="escola_origem" name="escola_origem" placeholder="Escola de origem" value="{{old('escola_origem')}}">
      </div>
      <div class="form-group col-md-6">
        <label for="naturalidade"><i class=""></i>Naturalidade</label>
        <input type="text" class="form-control" id="naturalidade" name="naturalidade" placeholder="naturalidade" v-model="$store.state.item.naturalidade">
      </div>       
      <div class="form-group col-md-3">
        <label for="telefone">Telefone</label>
        <input type="text" class="form-control" id="telefone" name="telefone" v-model="$store.state.item.telefone" placeholder="Telefone">
      </div>
      <div class="form-group col-md-3">
        <label for="encarregado2">Tel. Encar.</label>
        <input type="text" class="form-control" id="encarregado2" name="encarregado_tel" v-model="$store.state.item.encarregado_tel" placeholder="Tel. do Encaregado">
      </div>
      <div class="form-group col-md-6">
        <label for="email2">E-mail</label>
        <input type="email" class="form-control" id="email2" name="email" v-model="$store.state.item.email" placeholder="E-mail">
      </div>
      <div class="form-group col-md-12">
        <label for="descricao"><i class="glyphicon glyphicon-comment"></i>descricao</label>
        <input type="text" class="form-control" id="descricao" name="descricao" placeholder="descricao" v-model="$store.state.item.descricao">
      </div>      
    </formulario>
    <span slot="botoes">
      <button form="formEditar" class="btn btn-info">Atualizar</button>
    </span>
  </modal>


  
  <modal nome="detalhe" v-bind:titulo="$store.state.item.nome">    
    <p>@{{$store.state.item.pai}}</p>
  </modal>
@endsection
