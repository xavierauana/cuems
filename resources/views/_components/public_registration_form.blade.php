{{Form::open(['url' => url('delegates'),'method'=>"POST", 'id'=>"payment-form"])}}

	<input type="hidden" name="token" id="token" />
 
	<fieldset class="basic">
        <legend>Basic Information</legend>
        
        <div class="form-group row">
   
            <label class="col-sm-2 col-form-label">Title:</label>
            <div class="col-sm-10">
                <div class="form-check form-check-inline">
                  {{Form::radio('prefix', 'Prof', null, ['class'=>'form-check-input','required'])}}
	                <label class="form-check-label">Prof.</label>
                </div>
                <div class="form-check form-check-inline">
                  {{Form::radio('prefix', 'Dr', null, ['class'=>'form-check-input','required'])}}
	                <label class="form-check-label">Dr.</label>
                </div>
                <div class="form-check form-check-inline">
                  {{Form::radio('prefix', 'Mr', null, ['class'=>'form-check-input','required'])}}
	                <label class="form-check-label">Mr.</label>
                </div>
                <div class="form-check form-check-inline">
                  {{Form::radio('prefix', 'Ms', null, ['class'=>'form-check-input','required'])}}
	                <label class="form-check-label">Ms.</label>
                </div>
	            @if ($errors->has('prefix'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('prefix') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Gender:</label>
            <div class="col-sm-10">
                
                <div class="form-check form-check-inline">
	                {{Form::radio('is_male', '1', null, ['class'=>'form-check-input','required'])}}
	                <label class="form-check-label">Male</label>
                </div>
                <div class="form-check form-check-inline">
                  {{Form::radio('is_male', '0', null, ['class'=>'form-check-input','required'])}}
	                <label class="form-check-label">Female</label>
                </div>
	            @if ($errors->has('is_male'))
		            <span class="invalid-feedback" role="alert">
                        <strong>The gender field is required</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Surname</label>
            <div class="col-sm-10">
               {{Form::text('first_name', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('first_name'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Given Name</label>
            <div class="col-sm-10">
                 {{Form::text('last_name', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('last_name'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="position col-sm-2 col-form-label">Position</label>
            <div class="col-sm-10">
                 {{Form::text('position', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('position'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('position') }}</strong>
                        </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="department col-sm-2 col-form-label">Department</label>
            <div class="col-sm-10">
            
             {{Form::text('department', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('department'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('department') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="institution col-sm-2 col-form-label">Institution</label>
            <div class="col-sm-10">
                {{Form::text('institution', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('institution'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('institution') }}</strong>
                        </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="address col-sm-2 col-form-label">Address</label>
            <div class="col-sm-10">
                 {{Form::text('address', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('address'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="email col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
            {{Form::email('email', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('email'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="mobile col-sm-2 col-form-label">Mobile Tel</label>
            <div class="col-sm-10">
            {{Form::text('mobile', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('mobile'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('mobile') }}</strong>
                    </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="fax col-sm-2 col-form-label">Fax</label>
            <div class="col-sm-10">
                {{Form::text('fax', null, ['class'=>'form-control'])}}
	            @if ($errors->has('fax'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('fax') }}</strong>
                        </span>
	            @endif
            </div>
            
        </div>
      
     </fieldset>
    
    <fieldset class="ticket">
        <legend>Tickets:</legend>
         <div class="form-group row">
             <div class="col-sm-12">
                  @foreach(\App\Ticket::whereEventId(1)->available()->get() as $ticket)
		             @include("_components.tickets", compact('ticket'))
	             @endforeach
             </div>
         </div>
	
	    @if ($errors->has('ticket'))
		    <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('ticket') }}</strong>
            </span>
	    @endif
    </fieldset>
    
    <fieldset class="trainee">
        <legend>For Trainee</legend>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Training/Para-medical Organisation:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       name="training_organisation" />
	            @if ($errors->has('training_organisation'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('training_organisation') }}</strong>
                        </span>
	            @endif
            </div>
            
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Address</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       name="training_organisation_address" />
	            @if ($errors->has('training_organisation_address'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('training_organisation_address') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Name of supervisor:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       name="supervisor" />
	            @if ($errors->has('supervisor'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('supervisor') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Position:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       name="supervisor_position" />
	            @if ($errors->has('supervisor_postion'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('supervisor_postion') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
    </fieldset>
	
	<fieldset>
                    <legend>Payment</legend>
                    
                    <div class="form-group row">
                        <label class="fax col-sm-2 col-form-label">Credit or debit card</label>
                        <div class="col-sm-10">
                       <div id="card-element">
      <!-- A Stripe Element will be inserted here. -->
    </div>
                            <div id="card-errors" role="alert"></div>
                        </div>
                    </div>
                </fieldset>
                
     <button class="btn btn-primary">Submit</button>

{{Form::close()}}