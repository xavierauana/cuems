<div class="form-group">
    {!!  Form::label('schedule', 'Schedule'); !!}
	{!!  Form::text('schedule',null,['class'=>'form-control date']); !!}
	@if ($errors->has('schedule'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('schedule') }}</strong>
        </span>
	@endif
</div>