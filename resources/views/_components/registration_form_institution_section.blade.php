<fieldset class="institution">
        <legend>Institution</legend>
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
            <label class="institution col-sm-2 col-form-label">Institution/Hospital</label>
            <div class="col-sm-10">
                {{Form::select('institution', array_merge(["--- Please Select ---"], array_combine(\App\Institution::pluck('name')->toArray(),\App\Institution::pluck('name')->toArray()), ['other'=>"Other"]),null, ['class'=>'form-control select2', 'required'])}}
	            @if ($errors->has('institution'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('institution') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
        <div class="form-group row other_institution_container">
            <label class="institution col-sm-2 col-form-label">Or Other</label>
            <div class="col-sm-10">
                {{Form::text('other_institution', null, ['class'=>'form-control'])}}
	            @if ($errors->has('other_institution'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('other_institution') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
 </fieldset>

