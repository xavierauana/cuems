@component('admin._components.eventContainer', ['event'=>$event,'hasCkeditor'=>true])
	<div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Advertisement : {{$advertisement->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($advertisement, ['url' => route("events.advertisements.update", [$event,$advertisement]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate",'files'=>true]) !!}
	
	                @include("admin.events.advertisements._partials.form",['buttonText'=>'Update'])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
	
	@push('scripts')
		<script>
			function deleteImage(sender, imageId) {

              if (confirm("are you sure to delete the image?")) {
                var url = `/events/{{$event->id}}/advertisements/{{$advertisement->id}}/images/${imageId}`
                axios.delete(url)
                     .then(function (response) {
                       if (response.status === 200) {
                         sender.parentNode.remove()
                       } else {
                         alert('Something wrong! Please try again later.')
                       }
                     })
              }
            }
		</script>
	@endpush
@endcomponent