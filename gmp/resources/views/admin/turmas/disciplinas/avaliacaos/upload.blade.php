@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Importar  mini pauta a partir de um arquivo Excel para {{$turma->nome}}  - {{$disciplina->acronimo}}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{route('avaliacaos.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">

                                <div class="custom-file col-md-12 col-md-offset-5">

                                  <input type="file" multiple="multiple" class="custom-file-input" id="importar" name="excel_file[]" lang="eng" value="old(excel_file)">
                                  <label class="custom-file-label" for="importar">Excel</label>
                                        @if ($errors->has('title'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                </div>

                                <input type="hidden" name="turma" value="{{$turma->id}}">
                                <input type="hidden" name="disciplina" value="{{$disciplina->id}}">
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-5">
                                    <button type="submit" class="btn btn-success">
                                        Carregar Excel
                                    </button>
                                </div>
                            </div>                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
