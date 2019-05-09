@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Importa vários Professores de um arquivo Excel</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{route('professores.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <div class="custom-file col-md-12 col-md-offset-5">
                              <input type="file" class="custom-file-input" id="importar" name="excel_file" lang="eng" value="old(excel_file)">
                              <label class="custom-file-label" for="importar">Excel</label>
                              @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
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
