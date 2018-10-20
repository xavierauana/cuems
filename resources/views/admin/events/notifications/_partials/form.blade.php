<div class="form-group">
	{!!  Form::label('name', 'Name'); !!}
	{!!  Form::text('name',null,['class'=>'form-control']); !!}
	@if ($errors->has('name'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
	{!!  Form::label('event', 'System Event'); !!}
	{!!  Form::select('event',array_merge([0=>"-- Pick One --"],$events),null,['class'=>'form-control']); !!}
	@if ($errors->has('event'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('event') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('template', 'Template'); !!}
	{!!  Form::text('template',null,['class'=>'form-control']); !!}
	@if ($errors->has('template'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('template') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('from_name', 'From Name'); !!}
	{!!  Form::text('from_name',null,['class'=>'form-control']); !!}
	@if ($errors->has('from_name'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('from_name') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('from_email', 'From Email'); !!}
	{!!  Form::text('from_email',null,['class'=>'form-control']); !!}
	@if ($errors->has('from_email'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('from_email') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('subject', 'Subject'); !!}
	{!!  Form::text('subject',null,['class'=>'form-control']); !!}
	@if ($errors->has('subject'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('subject') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('role_id', 'Role'); !!}
	{!!  Form::select('role_id',array_merge([0=>"-- Pick One --"], \App\DelegateRole::pluck('label', 'id')->toArray()),null,['class'=>'form-control', 'step'=>"0.1"]); !!}
	@if ($errors->has('role_id'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('role_id') }}</strong>
        </span>
	@endif
</div>

@if(!isset($notificaiton))
	@include("admin.events.notifications._partials.createDates")
@else
	@include("admin.events.notifications._partials.editDates")
@endif


<div class="form-group">
    <input class="btn btn-success"
           type="submit"
           value="{{$buttonText}}" />
    <a href="{{route('events.notifications.index',$event)}}"
       class="btn btn-info text-light">Back</a>
</div>