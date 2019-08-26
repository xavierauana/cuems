<div class="form-group">
	{{Form::label('name','Name',['class'=>'form-label'])}}
	{{Form::text('name',null,['class'=>$errors->has('name')?"form-control is-invalid":"form-control"])}}
	@if ($errors->has('name'))
		<span class="invalid-feedback">
          <strong>{{ $errors->first('name') }}</strong>
      </span>
	@endif
</div>

<div class="form-group">
	<button class="btn btn-success" type="submit">{{$buttonText}}</button>
	<a class="btn btn-info text-light"
	   href="{{route('advertisement_types.index')}}">Back</a>
</div>

