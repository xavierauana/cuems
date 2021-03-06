@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Sponsor: {{$sponsor->name}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($sponsor, ['url' => route("events.sponsors.update", [$event, $sponsor]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.sponsors._partials.form", ['buttonText'=>"Update"])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
	
	@push('styles')
		<link rel="stylesheet"
		      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	@endpush
	
	@push('scripts')
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
		
		<script>
			_.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, {
              enableTime: true,
              dateFormat: "d M Y H:i",
            }))
		</script>
	@endpush

@endcomponent

