@extends('layouts.app')

@push('styles')
	<link rel="stylesheet"
	      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Event: {{$event->name}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($event,['url' => route("events.update", $event), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	                <div class="form-group">
		                {!!  Form::label('title', 'Title'); !!}
		                {!!  Form::text('title',null,['class'=>'form-control']); !!}
		                @if ($errors->has('title'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('title') }}</strong>
			                </span>
		                @endif
	                </div>
	                <div class="form-group">
		                {!!  Form::label('start_at', 'Start At'); !!}
		                {!!  Form::text('start_at',null,['class'=>'form-control date']); !!}
		                @if ($errors->has('start_at'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('start_at') }}</strong>
			                </span>
		                @endif
	                </div>
	                <div class="form-group">
		                {!!  Form::label('end_at', 'End At'); !!}
		                {!!  Form::text('end_at',null,['class'=>'form-control date']); !!}
		                @if ($errors->has('end_at'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('end_at') }}</strong>
			                </span>
		                @endif
	                </div>
	                <div class="form-group">
		                <input class="btn btn-success"
		                       type="submit"
		                       value="Create" />
		                <a href="{{route('events.index')}}"
		                   class="btn btn-info text-light">Back</a>
	                </div>
	
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection