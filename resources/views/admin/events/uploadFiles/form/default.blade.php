<div class="form-group">
	{{Form::label('file','File',['class'=>'form-label'])}}
	{{Form::file('file',['class'=>$errors->has('file')?"form-control is-invalid":"form-control"])}}
	@if ($errors->has('file'))
		<span class="invalid-feedback">
          <strong>{{ $errors->first('file') }}</strong>
      </span>
	@endif
</div>
<div class="form-group">
	<button class="btn btn-success" type="submit">{{$buttonText}}</button>
	<a class="btn btn-info text-light"
	   href="{{route('events.uploadFiles.index',$event)}}">Back</a>
</div>