@extends('layouts.app')
@section('content')
	<div class="container">
		<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Import to create positions</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("positions.import"), 'method'=>"POST", 'class'=>"needs-validation", "novalidate", "files"=>true]) !!}
	                <div class="form-group">
		                {!!  Form::label('file', 'File'); !!}
		                {!!  Form::file('file',['class'=>'form-control']); !!}
		                @if ($errors->has('file'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('file') }}</strong>
			                </span>
		                @endif
	                </div>
	                <div class="form-group">
		                <input class="btn btn-success"
		                       type="submit"
		                       value="Upload" />
		                <a href="{{route('positions.index')}}"
		                   class="btn btn-info text-light">Back</a>
	                </div>
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
	</div>
	
@endsection