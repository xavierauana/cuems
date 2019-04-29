<fieldset class="trainee" v-if="isTraineeTicket">
        <legend>To be filled out by Medical Trainee/Para-medic Participant</legend>
        <div class="form-group row">
                <label class="institution col-sm-3 col-form-label"><span
			                class="required-asterisk">*</span>Training/Para-medical Organisation:</label>
                <div class="col-sm-9">
                    {{Form::select('training_organisation', array_merge(["--- Please Select ---"],array_combine(\App\Institution::pluck('name')->toArray(),\App\Institution::pluck('name')->toArray())),null, ['class'=>'form-control select2', 'required', 'pattern'=>"[^0]+"])}}
	                @if ($errors->has('training_organisation'))
		                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('training_organisation') }}</strong>
                            </span>
	                @endif
                </div>
            </div>
        <div class="form-group row">
            <label class="institution col-sm-3 col-form-label"><span
			            class="required-asterisk">*</span>For others, please specify</label>
            <div class="col-sm-9">
                {{Form::text('training_other_organisation', null, ['class'=>'form-control'])}}
	            @if ($errors->has('training_other_organisation'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('training_other_organisation') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label"><span
			            class="required-asterisk">*</span>Name of supervisor:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::text("supervisor",null,['class'=>'form-control','required'])}}
	            @if ($errors->has('supervisor'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('supervisor') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label"><span
			            class="required-asterisk">*</span>Position:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::text("supervisor_position",null,['class'=>'form-control','required'])}}
	            @if ($errors->has('supervisor_position'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('supervisor_position') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
</fieldset>