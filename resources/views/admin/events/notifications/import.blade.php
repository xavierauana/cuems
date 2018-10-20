@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Upload Email Template</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("events.notifications.import", $event), 'method'=>"POST", 'class'=>"needs-validation", "novalidate", "files"=>true]) !!}
	                <div class="form-group">
		                {!!  Form::label('file', 'Template file'); !!}
		                <small>Should be .blade.php</small>
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
		                <a href="{{route('events.notifications.index', $event)}}"
		                   class="btn btn-info text-light">Back</a>
	                </div>
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
@endcomponent