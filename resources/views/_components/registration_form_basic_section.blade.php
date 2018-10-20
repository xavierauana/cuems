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

