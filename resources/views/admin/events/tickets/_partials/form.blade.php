<div class="form-group">
	{!!  Form::label('name', 'Ticket Name'); !!}
	{!!  Form::text('name',null,['class'=>'form-control']); !!}
	@if ($errors->has('name'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('name') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
	{!!  Form::label('code', 'Ticket Code'); !!}
	{!!  Form::text('code',null,['class'=>'form-control']); !!}
	@if ($errors->has('code'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('code') }}</strong>
		</span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('price', 'Price'); !!}
	{!!  Form::number('price',null,['class'=>'form-control', 'step'=>"0.1"]); !!}
	@if ($errors->has('price'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('price') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('vacancy', 'Number of seats'); !!}
	<small>Leave it blank if unlimited</small>
	{!!  Form::number('vacancy',null,['class'=>'form-control', 'step'=>"0.1"]); !!}
	@if ($errors->has('vacancy'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('vacancy') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('start_at', 'Start At'); !!}
	{!!  Form::text('start_at',null,['class'=>'form-control date']); !!}
	@if ($errors->has('start_at'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('start_at') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
    {!!  Form::label('end_at', 'End At'); !!}
	{!!  Form::text('end_at',null,['class'=>'form-control date']); !!}
	@if ($errors->has('end_at'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('end_at') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('note', 'Note'); !!}
	{!!  Form::textarea('note',null,['class'=>'form-control']); !!}
	@if ($errors->has('note'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('note') }}</strong>
        </span>
	@endif
</div>
<div class="form-group">
 
	 <span class="switch">
	 {{Form::checkbox('is_public',1, null,['class'=>'switch','id'=>'switch-normal'])}}
		 <label for="switch-normal">Is Public</label>
	 </span>
	
	{{--	{!!  Form::checkbox('is_public',1,null, ['class'=>'form-control']); !!}--}}
	@if ($errors->has('is_public'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('is_public') }}</strong>
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