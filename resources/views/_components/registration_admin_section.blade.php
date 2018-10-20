

<fieldset class="transaction">
        <legend>Delegate Role</legend>
    
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Role:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::select('roles_id[]',\App\DelegateRole::pluck('label','id'), null, ['class'=>'form-control', 'multiple'])}}
	            @if ($errors->has('roles_id[]'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('roles_id[]') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
</fieldset>
<fieldset class="transaction">
        <legend>Transaction Status</legend>
    
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Transaction Status:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::select('status',$status, null, ['class'=>'form-control'])}}
	            @if ($errors->has('status'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Note:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::textarea('note', null, ['class'=>'form-control'])}}
	            @if ($errors->has('note'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('note') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
</fieldset>

