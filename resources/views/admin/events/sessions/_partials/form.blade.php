<div class="form-group">
		                {!!  Form::label('title', 'Title'); !!}
	{!!  Form::text('title',null,['class'=>'form-control']); !!}
	@if ($errors->has('title'))
		<span class="invalid-feedback" role="alert">
				                <strong>{{ $errors->first('title') }}</strong>
			                </span>
	@endif
	                </div>
<div class="form-group">
    {!!  Form::label('subtitle', 'Sub Title'); !!}
	{!!  Form::text('subtitle',null,['class'=>'form-control']); !!}
	@if ($errors->has('subtitle'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('subtitle') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('venue', 'Venue'); !!}
	{!!  Form::text('venue',null,['class'=>'form-control','required']); !!}
	@if ($errors->has('venue'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('venue') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('sponsor', 'Sponsor'); !!}
	{!!  Form::text('sponsor', null,['class'=>'form-control']); !!}
	@if ($errors->has('sponsor'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('sponsor') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('moderation_type', 'Moderation Type'); !!}
	{!!  Form::select('moderation_type',array_flip(\App\Enums\SessionModerationType::getTypes()), null,['class'=>'form-control']); !!}
	@if ($errors->has('moderation_type'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('moderation_type') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('moderators[]', 'Moderators'); !!}
	{!!  Form::select('moderators[]',$delegates->pluck('name','id') , null,['class'=>'form-control select2', 'multiple']); !!}
	@if ($errors->has('moderators'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('moderators') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('start_at', 'Start At'); !!}
	{!!  Form::text('start_at',null,['class'=>'form-control date-time']); !!}
	@if ($errors->has('start_at'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('start_at') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('end_at', 'End At'); !!}
	{!!  Form::text('end_at',null,['class'=>'form-control date-time']); !!}
	@if ($errors->has('end_at'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('end_at') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    <input class="btn btn-success"
           type="submit"
           value="{{$buttonText}}" />
    <a href="{{route('events.sessions.index',$event)}}"
       class="btn btn-info text-light">Back</a>
</div>