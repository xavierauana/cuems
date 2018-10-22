<div class="form-group">
		                {!!  Form::label('title', 'Topic'); !!}
	{!!  Form::text('title',null,['class'=>'form-control']); !!}
	@if ($errors->has('title'))
		<span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('title') }}</strong>
			                </span>
	@endif
	                </div>
<div class="form-group">
    {!!  Form::label('speakers[]', 'Speakers'); !!}
	{!!  Form::select('speakers[]',$delegates ,null,['class'=>'form-control select2', 'multiple']); !!}
	@if ($errors->has('speakers'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('speakers') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    <input class="btn btn-success"
           type="submit"
           value="{{$buttonText}}" />
    <a href="{{route('events.sessions.talks.index',[$event, $session])}}"
       class="btn btn-info text-light">Back</a>
</div>