@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">The record: {{$record->invoice_id}}</div>
		        <div class="card-body">
			        @foreach(json_decode($record->form_data, true) as $key=>$val)
				        {{app(\App\Presenters\RegistrationDataPresenter::class)($key, $val)}}
			        @endforeach
		        </div>
		        <div class="card-footer">
			        <a href="{{route('events.payment_records.convert',[$event->id, $record->id])}}"
			           class="btn btn-success">Create Delegate</a>
		        </div>
            </div>
	        
        </div>
    </div>
@endcomponent
