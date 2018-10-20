<div class="form-group">
    {!!  Form::label('start_at', 'Start At'); !!}
	{!!  Form::text('start_at',null,['class'=>'form-control date']); !!}
	@if ($errors->has('start_at'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('start_at') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('end_at', 'End At'); !!}
	{!!  Form::text('end_at',null,['class'=>'form-control date']); !!}
	@if ($errors->has('end_at'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('end_at') }}</strong>
        </span>
	@endif
</div>