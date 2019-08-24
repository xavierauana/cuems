@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New Session for Event: {{$event->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("events.sessions.store", $event), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.sessions._partials.form",['buttonText'=>'Create'])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
	@push('scripts')
		@include("_partials.ckeditor_script")
	@endpush
@endcomponent