<div class="form-group">
	{!!  Form::label('key', 'Key'); !!}
	{!!  Form::text('key',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('key'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('key') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
	{!!  Form::label('value', 'Value'); !!}
	{!!  Form::textarea('value',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('value'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('value') }}</strong>
		</span>
	@endif
</div>
<div class="form-group">
	<input class="btn btn-success"
	       type="submit"
	       value="{{$buttonText}}" />
	<a href="{{route('events.settings.index',$event)}}"
	   class="btn btn-info text-light">Back</a>
</div>