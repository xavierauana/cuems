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
                <div class="card-header">Create New Session for Event: {{$event->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("events.sessions.store", $event), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
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
		                {!!  Form::label('subtitle', 'Sub Title'); !!}
		                {!!  Form::text('subtitle',null,['class'=>'form-control']); !!}
		                @if ($errors->has('subtitle'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('subtitle') }}</strong>
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

@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	
	<script>
		
		
		_.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, {
          enableTime: true,
          dateFormat: "d M Y H:i",
          minDate   : "{{$event->start_at->format('d M Y')}}",
          maxDate   : "{{$event->end_at->format('d M Y')}}",
        }))
	</script>
@endpush
