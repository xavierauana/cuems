<div class="form-group">
	{!!  Form::label('name', 'Name'); !!}
	{!!  Form::text('name',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('name'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('name') }}</strong>
		</span>
	@endif
</div>
<div class="form-group">
	<input class="btn btn-success"
	       type="submit"
	       value="{{$buttonText}}" />
	<a href="{{route('institutions.index')}}"
	   class="btn btn-info text-light">Back</a>
</div>