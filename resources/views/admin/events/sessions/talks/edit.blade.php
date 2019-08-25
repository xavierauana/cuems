@component('admin._components.eventContainer', ['event'=>$event,'hasCkeditor'=>true])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Talk: {{$talk->title}}</div>
                
                <div class="card-body">
	                {!! Form::model($talk, ['url' => route("events.sessions.talks.update", [$event, $session, $talk]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include('admin.events.sessions.talks._partials.form',['buttonText'=>'Update'])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
@endcomponent