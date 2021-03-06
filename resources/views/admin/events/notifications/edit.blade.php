@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Notification: {{$notification->name}}
	                @if($notification->type ==='attendee')
	                                     (To Attendees)
	                @endif</div>
                
                <div class="card-body">
	                
	                {!! Form::model($notification, ['url' => route("events.notifications.update", [$event, $notification]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.notifications._partials.form", ['buttonText'=>"Update"])
	
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
			var options = {
              enableTime   : true,
              dateFormat   : "d M Y H:i",
				@if(isset($notification) and $date = optional($notification->schedule)->format("d M Y H:i"))
                defaultDate: "{{$date}}"
				@endif
            }
            _.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, options))
		</script>
	@endpush

@endcomponent

