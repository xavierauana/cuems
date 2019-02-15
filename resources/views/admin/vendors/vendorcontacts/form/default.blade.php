<div class="form-group">
	{{Form::label('name','Name',['class'=>'form-label'])}}
	{{Form::text('name',null,['class'=>$errors->has('name')?"form-control is-invalid":"form-control",'required'])}}
	@if ($errors->has('name'))
		<span class="invalid-feedback">
          <strong>{{ $errors->first('name') }}</strong>
      </span>
	@endif
</div>

<div class="form-group">
	{{Form::label('email','Email',['class'=>'form-label'])}}
	{{Form::email('email',null,['class'=>$errors->has('email')?"form-control is-invalid":"form-control"])}}
	@if ($errors->has('email'))
		<span class="invalid-feedback">
          <strong>{{ $errors->first('email') }}</strong>
      </span>
	@endif
</div>

<div class="form-group">
	{{Form::label('tel','Telephone',['class'=>'form-label'])}}
	{{Form::text('tel',null,['class'=>$errors->has('tel')?"form-control is-invalid":"form-control"])}}
	@if ($errors->has('tel'))
		<span class="invalid-feedback">
          <strong>{{ $errors->first('tel') }}</strong>
      </span>
	@endif
</div>

<div class="form-group">
	{{Form::label('fax','Fax',['class'=>'form-label'])}}
	{{Form::text('fax',null,['class'=>$errors->has('fax')?"form-control is-invalid":"form-control"])}}
	@if ($errors->has('fax'))
		<span class="invalid-feedback">
          <strong>{{ $errors->first('fax') }}</strong>
      </span>
	@endif
</div>



<div class="form-group">
	<button class="btn btn-success" type="submit">{{$buttonText}}</button>
	<a class="btn btn-info text-light"
	   href="{{route('vendors.vendorContacts.index',$vendor)}}">Back</a>
</div>