@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New Expense for Event: {{$event->title}}</div>
                
                <div class="card-body">
	                
	                {!! Form::open(['url' => route("events.expenses.store", $event), 'method'=>"POST", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.expenses._partials.form", ['buttonText'=>"Create"])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
	
	@push('scripts')
		
		<script>
		_.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, {
          dateFormat: "d M Y",
        }))
	</script>
	@endpush

@endcomponent
