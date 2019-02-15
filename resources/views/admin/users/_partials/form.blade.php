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
	{!!  Form::label('email', 'Email'); !!}
	{!!  Form::email('email',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('email'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('email') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
	{!!  Form::label('password', 'Password'); !!}
	{!!  Form::password('password',['class'=>'form-control']); !!}
	@if ($errors->has('password'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('password') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
	{!!  Form::label('password_confirmation', 'Password again'); !!}
	{!!  Form::password('password_confirmation',['class'=>'form-control']); !!}
	@if ($errors->has('password_confirmation'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('password_confirmation') }}</strong>
		</span>
	@endif
</div>
<div class="form-group">
	<input class="btn btn-success"
	       type="submit"
	       value="{{$buttonText}}" />
	<a href="{{route('users.index')}}"
	   class="btn btn-info text-light">Back</a>
</div>