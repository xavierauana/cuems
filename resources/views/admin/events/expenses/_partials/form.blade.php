<div class="form-group">
	{!!  Form::label('amount', 'Amount'); !!}
	{!!  Form::number('amount',null,['class'=>'form-control', 'min'=>0,'step'=>0.1,'required']); !!}
	@if ($errors->has('amount'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('amount') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
	{!!  Form::label('vendor_id', 'Vendor'); !!}
	{!!  Form::select('vendor_id',$vendors,null,['class'=>'form-control select2-tag']); !!}
	@if ($errors->has('vendor_id'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('vendor_id') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
	{!!  Form::label('category_id', 'Category'); !!}
	{!!  Form::select('category_id',$categories,null,['class'=>'form-control select2-tag']); !!}
	@if ($errors->has('category_id'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('category_id') }}</strong>
		</span>
	@endif
</div>

<div class="form-group">
    {!!  Form::label('date', 'Date'); !!}
	{!!  Form::text('date',null,['class'=>'form-control date']); !!}
	@if ($errors->has('date'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('date') }}</strong>
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
    {!!  Form::label('attachments', 'Attachments'); !!}
	<div class="dropzone-preview mb-3">
		@if(isset($expense))
			<div class="row">
			@foreach($expense->files as $file)
					<div class="col-4">
						<div class="card">
							<img class="img-fluid"
							     src="/expenses/{{$expense->id}}/{{$file->path}}">
							<div class="card-img-overlay p-1">
									<button type="submit"
									        onclick="deleteImage(event, {{$file->id}})"
									        class="card-link float-right btn btn-danger btn-sm text-light"><i
												class="fa fa-times"></i></button>
							  </div>
						</div>
					</div>
				@endforeach
			</div>
		@endif
	</div>
	<div class="dropzone mb-3"></div>
	@if ($errors->has('attachments'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('attachments') }}</strong>
		</span>
	@endif
	</div>

<div class="form-group file-input">
    <input class="btn btn-success"
           type="submit"
           value="{{$buttonText}}" />
    <a href="{{route('events.expenses.index',$event)}}"
       class="btn btn-info text-light">Back</a>
</div>

@push('scripts')
	
	<script>
		new Dropzone("div.dropzone", {
          url    : "/files",
          sending: (file, xhr, formData) => {
            formData.append('_token', "{{csrf_token()}}")
          },
          success: (file, response) => {

            var input = document.createElement("input")
            input.name = "files[]"
            input.type = 'hidden'
            input.value = response.path
            var el = document.querySelector(".file-input")

            el.appendChild(input)
          }
        });
		
		@if(isset($expense))
        function deleteImage(e, fileId) {
          e.preventDefault()
          if (confirm("Are to sure to delete the attachment?")) {
            var form        = document.createElement("Form"),
                csrfInput   = document.createElement("Input"),
                methodInput = document.createElement("Input")
            csrfInput.name = "_token"
            csrfInput.value = "{{csrf_token()}}"
            methodInput.name = "_method"
            methodInput.value = "DELETE"


            form.action = "/events/{{$event->id}}/expenses/{{$expense->id}}/files/" + fileId
            form.method = "POST"

            form.appendChild(methodInput)
            form.appendChild(csrfInput)

            document.querySelector("body").appendChild(form)
            form.submit()
          }
        }
		@endif
	</script>

@endpush