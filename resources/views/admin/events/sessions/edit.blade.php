@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Session : {{$session->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($session, ['url' => route("events.sessions.update", [$event,$session]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.sessions._partials.form",['buttonText'=>'Update'])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
@endcomponent