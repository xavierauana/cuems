@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Edit Delegate: {{$delegate->name}}</div>
                
                <div class="card-body">
	                
	                {{Form::model($delegate, ['url' => route("events.delegates.update", [$event,$delegate]),'method'=>"PUT", 'id'=>"payment-form"])}}
	
	                @include("_components.registration_form_basic_section")
	
	                @include("_components.registration_form_institution_section")
	
	                @include("_components.registration_trainee_section")
	
	                @include('_components.admin_registration_ticket_section')
	
	                @include('_components.registration_admin_section', compact('status'))
	
	                <button class="btn btn-primary">Update</button>
	
	                {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endcomponent