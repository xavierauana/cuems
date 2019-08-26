<div class="form-group">
    {!!  Form::label('buyer', 'Buyer'); !!}
	{!!  Form::text('buyer',null,['class'=>'form-control']); !!}
	@if ($errors->has('buyer'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('buyer') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
	@if(isset($advertisement) and $media = $advertisement->getFirstMedia('logo') )
		<div class="row">
			<div class="col-md-3 col-sm-4 col-6 img-container"
			     style="position: relative;display: inline-block">
			<img class="img-fluid" src="{{$media->getFullUrl()}}" />
			<button class="btn btn-danger btn-sm"
			        type="button"
			        onclick="deleteImage(this, {{$media->id}})"
			        style="position: absolute; bottom:5px; right: 20px"><i
						class="fa fa-times"></i></button>
			</div>
		</div>
		<br>
	@endif
	{!!  Form::label('logo', 'Logo'); !!}
	{!!  Form::file('logo',['class'=>'form-control']); !!}
	@if ($errors->has('logo'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('logo') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
	@if(isset($advertisement) and $items = $advertisement->getMedia('banners') )
		<div class="row">
			@foreach($items as $media)
				<div class="col-md-3 col-sm-4 col-6 img-container"
				     style="position: relative;display: inline-block">
				<img class="img-fluid"
				     src="{{$media->getFullUrl()}}" />
				<button class="btn btn-danger btn-sm"
				        type="button"
				        onclick="deleteImage(this, {{$media->id}})"
				        style="position: absolute; bottom:5px; right: 20px"><i
							class="fa fa-times"></i></button>
				</div>
			@endforeach
			
		</div>
		<br>
	@endif
	{!!  Form::label('banners[]', 'Banners'); !!}
	{!!  Form::file('banners[]',['class'=>'form-control','multiple']); !!}
	@if ($errors->has('banners[]'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('banners[]') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('description', 'Description'); !!}
	{!!  Form::textarea('description',null,['class'=>'form-control ckeditor','multiple']); !!}
	@if ($errors->has('description'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('description') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('type_id', 'Type'); !!}
	{!!  Form::select('type_id',\App\AdvertisementType::pluck('name','id'),null,['class'=>'form-control','required']); !!}
	@if ($errors->has('type_id'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('type_id') }}</strong>
        </span>
	@endif
</div>

<div class="form-group">
    <input class="btn btn-success"
           type="submit"
           value="{{$buttonText}}" />
    <a href="{{route('events.advertisements.index',$event)}}"
       class="btn btn-info text-light">Back</a>
</div>