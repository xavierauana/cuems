{{Form::hidden('type','attendee')}}
{{Form::hidden('keyword',request()->query('keyword'))}}
{{Form::hidden('check_in_date',request()->query('date'))}}

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
	{!!  Form::email('from_email',null,['class'=>'form-control']); !!}
	@if ($errors->has('from_email'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('from_email') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('cc', 'CC'); !!}
	{!!  Form::email('cc',null,['class'=>'form-control']); !!}
	@if ($errors->has('cc'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('cc') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('bcc', 'BCC'); !!}
	{!!  Form::email('bcc',null,['class'=>'form-control']); !!}
	@if ($errors->has('bcc'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('bcc') }}</strong>
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
    {!!  Form::label('files[]', 'Attachments'); !!}
	{!!  Form::select('files[]',$files,null,['class'=>'form-control select2','multiple', 'style'=>'width:100%']); !!}
	@if ($errors->has('files[]'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('files[]') }}</strong>
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

<div class="form-group">
	<div class="row">
		<div class="col-6">
			{!!  Form::label('check_in_date', 'Checkin Date'); !!}
			{!!  Form::text('check_in_date',request()->query('date'),['class'=>'form-control','disabled' ]); !!}
			@if ($errors->has('check_in_date'))
				<span class="invalid-feedback" role="alert">
		            <strong>{{ $errors->first('check_in_date') }}</strong>
                </span>
			@endif
		</div>
		<div class="col-6">
			{!!  Form::label('keyword', 'Keyword'); !!}
			{!!  Form::text('keyword',request()->query('keyword'),['class'=>'form-control','disabled' ]); !!}
			@if ($errors->has('keyword'))
				<span class="invalid-feedback" role="alert">
		            <strong>{{ $errors->first('keyword') }}</strong>
                </span>
			@endif
		</div>
	</div>
</div>

@if(!isset($notification))
	@include("admin.events.notifications._partials.createDates")
@else
	@include("admin.events.notifications._partials.editDates")
@endif