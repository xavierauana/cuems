<fieldset class="institution">
    <div class="form-group row">
            <label class="position col-sm-2 col-form-label"><span class="required-asterisk">*</span>Position</label>
            <div class="col-sm-10">
                @php
	                $positions = getPositionList()->toArray();
                @endphp
	            {{Form::select('position',['--- Please Select ---']+ array_combine($positions,$positions)  ,null, ['class'=>'form-control select2', 'required'])}}
	            @if ($errors->has('position'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('position') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
    <div class="form-group row" id="other_position_container">
            <label class="position col-sm-2 col-form-label"><span class="required-asterisk">*</span>For others, please specify</label>
            <div class="col-sm-10">
                @php
	                $positions = getPositionList()->toArray();
                @endphp
	            {{Form::text('other_position',null, ['class'=>'form-control'])}}
	            @if ($errors->has('other_position'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('other_position') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
    
      <div class="form-group row">
            <label class="department col-sm-2 col-form-label"><span class="required-asterisk">*</span>Department</label>
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
            <label class="institution col-sm-2 col-form-label"><span class="required-asterisk">*</span>Institution/Hospital</label>
            <div class="col-sm-10">
                {{Form::select('institution', array_merge(["--- Please Select ---"], array_combine(\App\Institution::pluck('name')->toArray(),\App\Institution::pluck('name')->toArray())),null, ['class'=>'form-control select2', 'required'])}}
	            @if ($errors->has('institution'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('institution') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
        <div class="form-group row other_institution_container">
            <label class="institution col-sm-2 col-form-label"><span class="required-asterisk">*</span>For others, please specify</label>
            <div class="col-sm-10">
                {{Form::text('other_institution', null, ['class'=>'form-control'])}}
	            @if ($errors->has('other_institution'))
		            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('other_institution') }}</strong>
                        </span>
	            @endif
            </div>
        </div>
    
      <div class="form-group row">
            <label class="address col-sm-2 col-form-label"><span class="required-asterisk">*</span>Address 1</label>
            <div class="col-sm-10">
                 {{Form::text('address_1', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('address_1'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('address_1') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="address col-sm-2 col-form-label"><span class="required-asterisk">*</span>Address 2</label>
            <div class="col-sm-10">
                 {{Form::text('address_2', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('address_2'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('address_2') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="address col-sm-2 col-form-label"><span class="required-asterisk">*</span>Address 3</label>
            <div class="col-sm-10">
                 {{Form::text('address_3', null, ['class'=>'form-control', 'required'])}}
	            @if ($errors->has('address_3'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('address_3') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
    
    
        <div class="form-group row">
            <label class="country col-sm-2 col-form-label"><span class="required-asterisk">*</span>Country/Region</label>
            <div class="col-sm-10">
            {{Form::select('country',getCountiesList() ,'Hong Kong SAR China', ['class'=>'form-control select2', 'required'])}}
	            @if ($errors->has('country'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('country') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
 </fieldset>

