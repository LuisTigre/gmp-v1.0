@extends('layouts.app')

@section('content')

    <pagina tamanho="12">
        <painel>
           <h2 align="center">{{$artigo->titulo}}</h2>
           <h4 align="center">{{$artigo->descricao}}</h4>
           </br></br></br></br>
           <p>{!!$artigo->conteudo!!}</p>
           <p align="center">
                <small >Por: {{$artigo->user->name}} - {{$artigo->data}}</small>
           </p>

            
        </painel>
    </pagina>

@endsection
