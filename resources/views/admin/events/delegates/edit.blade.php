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
	
	
	                <!-- Normal switch -->
		                <div class="form-group">
						  <span class="switch">
							  {{Form::checkbox('is_duplicated',\App\Enums\DelegateDuplicationStatus::DUPLICATED, $delegate->is_duplicated === \App\Enums\DelegateDuplicationStatus::DUPLICATED,['class'=>'switch','id'=>'switch-normal'])}}
							  <label for="switch-normal">Is Duplicated</label>
						  </span>
			                @if ($errors->has('is_duplicated'))
				                <span class="invalid-feedback" role="alert">
						            <strong>{{ $errors->first('is_duplicated') }}</strong>
						        </span>
			                @endif
						</div> <!-- Normal switch -->
		                <div class="form-group">
						  <span class="switch">
							  {{Form::checkbox('is_verified',true, $delegate->is_verified ,['class'=>'switch','id'=>'is-verified-switch'])}}
							  <label for="is-verified-switch">Is Verified</label>
						  </span>
			                @if ($errors->has('is_verified'))
				                <span class="invalid-feedback" role="alert">
						            <strong>{{ $errors->first('is_verified') }}</strong>
						        </span>
			                @endif
						</div>
	
	                <button class="btn btn-primary">Update</button>
		
		                {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endcomponent