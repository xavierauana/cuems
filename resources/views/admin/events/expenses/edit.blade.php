@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit expense: {{$expense->name}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($expense, ['url' => route("events.expenses.update", [$event->id, $expense->id]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	
	                @include("admin.events.expenses._partials.form", ['buttonText'=>"Update"])
	
	                {!! Form::close() !!}
	                
                </div>
            </div>
        </div>
    </div>
	
	@push('scripts')
		
		<script>
			_.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, {
              enableTime: true,
              dateFormat: "d M Y H:i",
            }))
		</script>
	@endpush

@endcomponent

