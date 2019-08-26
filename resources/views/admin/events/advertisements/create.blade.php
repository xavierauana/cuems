@component('admin._components.eventContainer', ['event'=>$event,'hasCkeditor'=>true])
	<div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New Advertisement for Event: {{$event->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("events.advertisements.store", $event), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.advertisements._partials.form",['buttonText'=>'Create'])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
@endcomponent