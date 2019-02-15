<div class="form-group">
	{!!  Form::label('label', 'Name'); !!}
	{!!  Form::text('label',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('label'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('label') }}</strong>
		</span>
	@endif
</div><div class="form-group">
	{!!  Form::label('code', 'Code'); !!}
	{!!  Form::text('code',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('code'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('code') }}</strong>
		</span>
	@endif
</div>

<!-- Normal switch -->
<div class="form-group">
  <span class="switch">
	  {{Form::checkbox('is_default',1, null,['class'=>'switch','id'=>'switch-normal'])}}
	  <label for="switch-normal">Is Default</label>
  </span>
</div>

<div class="form-group">
	<input class="btn btn-success"
	       type="submit"
	       value="{{$buttonText}}" />
	<a href="{{route('roles.index')}}"
	   class="btn btn-info text-light">Back</a>
</div>