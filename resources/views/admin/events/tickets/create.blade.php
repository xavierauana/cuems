@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New Ticket for Event: {{$event->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("events.tickets.store", $event), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
	                <div class="form-group">
		                {!!  Form::label('name', 'Ticket Name'); !!}
		                {!!  Form::text('name',null,['class'=>'form-control']); !!}
		                @if ($errors->has('name'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('name') }}</strong>
			                </span>
		                @endif
	                </div>
	                <div class="form-group">
		                {!!  Form::label('price', 'Price'); !!}
		                {!!  Form::number('price',null,['class'=>'form-control', 'step'=>"0.1"]); !!}
		                @if ($errors->has('price'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('price') }}</strong>
			                </span>
		                @endif
	                </div>
	                <div class="form-group">
		                {!!  Form::label('vacancy', 'Number of seats'); !!}
		                <small>Leave it blank if unlimited</small>
		                {!!  Form::number('vacancy',null,['class'=>'form-control', 'step'=>"0.1"]); !!}
		                @if ($errors->has('vacancy'))
			                <span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('vacancy') }}</strong>
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
@endcomponent

