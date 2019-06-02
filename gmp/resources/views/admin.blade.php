@extends('layouts.app')

@section('content')

    <pagina tamanho="8">
	    <painel titulo="Dashboard">
	    	<migalhas v-bind:lista="{{$listaMigalhas}}"></migalhas>
	    	<div class="row">
	    		@can('professor')	    		
	            <div class="col-md-2">
	                <caixa titulo="Professores" qtd="{{$totalProfessores}}" url="{{route('professores.index')}}" cor="brown" icon="ion ion-person-stalker"></caixa>
	            </div>	
	            <div class="col-md-2">
	                <caixa titulo="Turmas" qtd="{{$totalTurmas}}" url="{{route('turmas.index')}}" cor="darkblue" icon="ion ion-person-stalker"></caixa>
	            </div>
	    		@endcan
	    		@can('eAdmin')
	    		<div class="col-md-2">
	                <caixa titulo="Artigos" qtd="{{$totalArtigos}}" url="{{route('artigos.index')}}" cor="brown" icon="glyphicon glyphicon-comment"></caixa>
	            </div>
	    		<div class="col-md-2">
	                <caixa titulo="Usuários" qtd="{{$totalUsuarios}}" url="{{route('usuarios.index')}}" cor="brown" icon="ion ion-person-stalker">	                	
	                </caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Autores" qtd="{{$totalAutores}}" url="{{route('autores.index')}}" cor="brown" icon="ion ion-person"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Admin" qtd="{{$totalAdmin}}" url="{{route('adm.index')}}" cor="brown" icon="ion ion-person"></caixa>
	            </div>	
	            <!-- <div class="col-md-2">
	                <caixa titulo="Horário" qtd="{{$totalAdmin}}" url="{{route('horario.index')}}" cor="brown" icon="glyphicon glyphicon-time"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Recursos" qtd="{{$totalAdmin}}" url="{{route('recursos.index')}}" cor="brown" icon="glyphicon glyphicon-book"></caixa>
	            </div> -->
	            <div class="col-md-2">
	                <caixa titulo="Áreas" qtd="{{$totalAreas}}" url="{{route('areas.index')}}" cor="brown" icon="glyphicon glyphicon-book"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Instituição" qtd="{{1}}" url="{{route('instituicaos.index')}}" cor="brown" icon="glyphicon glyphicon-book"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Alunos" qtd="{{$totalAlunos}}" url="{{route('alunos.index')}}" cor="darkgreen" icon="ion ion-person-stalker"></caixa>
	            </div>	                        
	            <div class="col-md-2">
	                <caixa titulo="Cursos" qtd="{{$totalCursos}}" url="{{route('cursos.index')}}" cor="brown" icon="glyphicon glyphicon-education"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Disciplinas" qtd="{{$totalDisciplinas}}" url="{{route('disciplinas.index')}}" cor="brown" icon="glyphicon glyphicon-book"></caixa>
	            </div>	
	            <div class="col-md-2">
	                <caixa titulo="Classes" qtd="{{$totalClasses}}" url="{{route('classes.index')}}" cor="brown" icon="glyphicon glyphicon-book"></caixa>
	            </div>		
	            <div class="col-md-2">
	                <caixa titulo="Modulos" qtd="{{$totalModulos}}" url="{{route('modulos.index')}}" cor="brown" icon="glyphicon glyphicon-book"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Devedores" qtd="{{$totalDevedores}}" url="{{route('devedores.index')}}" cor="brown" icon="fa fa-money"></caixa>
	            </div>
	            <div class="col-md-2">
	                <caixa titulo="Anos Acad." qtd="{{$totalClasses}}" url="{{route('epocas.index')}}" cor="brown" icon="glyphicon glyphicon-calendar"></caixa>
	            </div>	
	            <div class="col-md-2">
	                <caixa titulo="Salas" qtd="{{$totalSalas}}" url="{{route('salas.index')}}" cor="brown" icon="glyphicon glyphicon-home"></caixa>
	            </div>				
	    		@endcan

	    		
			</div>
	    </painel>
    </pagina>

@endsection
