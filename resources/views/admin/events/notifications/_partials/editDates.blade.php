<div class="form-group">
    {!!  Form::label('schedule', 'Schedule'); !!}
	{!!  Form::text('schedule',optional($notification->schedule)->format("d M Y H:i"),['class'=>'form-control date']); !!}
	@if ($errors->has('schedule'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('schedule') }}</strong>
        </span>
	@endif
</div>