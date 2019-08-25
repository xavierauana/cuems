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
    {!!  Form::label('order', 'Order'); !!}
	{!!  Form::number('order' ,null,['class'=>'form-control','min'=>0, 'step'=>1]); !!}
	@if ($errors->has('order'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('order') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('extra_attributes[description]', 'Description'); !!}
	{!!  Form::textarea('extra_attributes[description]' ,null,['class'=>'ckeditor form-control']); !!}
	@if ($errors->has('extra_attributes[description]'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('extra_attributes[description]') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    <input class="btn btn-success"
           type="submit"
           value="{{$buttonText}}" />
    <a href="{{url()->previous()}}"
       class="btn btn-info text-light">Back</a>
</div>