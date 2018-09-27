@component('_components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New Talk for Session: {{$session->title}}</div>
                
                <div class="card-body">
	                {!! Form::open(['url' => route("events.sessions.talks.store", [$event, $session]), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
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
@endcomponent