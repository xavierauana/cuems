@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New Talk for Session: {{$session->title}}</div>
                
                <div class="card-body">
	                {!! Form::open(['url' => route("events.sessions.talks.store", [$event, $session]), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
	                
	                @include('admin.events.sessions.talks._partials.form',['buttonText'=>'Create'])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
@endcomponent