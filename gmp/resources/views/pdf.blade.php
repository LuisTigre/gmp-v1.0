@extends('layouts.app')

@section('content')

    <pagina tamanho="12">
        <painel titulo="Artigos">

          <p>
            <form class="form-inline text-center" action="{{route('site')}}" method="get" >
              <input type="search" class="form-control" name="busca" placeholder="Buscar" value="{{isset($busca) ? $busca : ""}}">
              <button class="btn btn-info">Buscar</button>
            </form>
          </p>

           <div class="row">
            
               <artigocard 
                titulo="{{str_limit('We the Best',25,'...')}}"
                descricao="{{str_limit('This is wtb records',40,'...')}}"
                link="www.google.com"
                imagem="https://coletiva.net/files/e4da3b7fbbce2345d7772b0674a318d5/midia_foto/20170713/118815-maior_artigo_jul17.jpg"
                data="14/11/2014"
                autor="Luís Tigre"
                sm="6"
                md="4">
               </artigocard> 
            
                
           </div>

            <div align="center">
               
            </div>
        </painel>
    </pagina>

@endsection
