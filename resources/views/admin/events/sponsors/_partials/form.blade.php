<div class="form-group">
	{!!  Form::label('name', 'Sponsor Name'); !!}
	{!!  Form::text('name',null,['class'=>'form-control']); !!}
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
    <a href="{{route('events.tickets.index',$event)}}"
       class="btn btn-info text-light">Back</a>
</div>