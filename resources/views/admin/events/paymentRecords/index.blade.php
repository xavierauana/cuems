<?php
$status = array_flip((new \ReflectionClass(\App\Enums\TransactionStatus::class))->getConstants());
?>
@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Failed payment records for event: {{$event->title}}</div>
                
		        <div class="row mt-3 mx-3">
			        <div class="col-md-6 float-right">
				        <form action="{{route('events.payment_records.index', $event)}}"
				              method="GET">
					        
					        <div class="input-group mb-3">
							  <input type="text" class="form-control"
							         name="keyword"
							         placeholder="Keyword"
							         aria-label="Keyword"
							         value="{{request()->query('keyword')}}"
							  >
							  <div class="input-group-append">
							    <button class="btn btn-outline-secondary"
							            type="submit">Search</button>
							  </div>
							</div>
				        </form>
			        </div>
		        </div>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>#</th>
                        <th>Invoice Id</th>
                        <th>Email</th>
                        <th>Status</th>
                    </thead>
	                  <tbody>
	                  @foreach($records as $index=>$record)
		                  <tr>
			                  <td>{{$records->firstItem()+$index}}</td>
			                  <td><a href="{{route('events.payment_records.show',[$event->id, $record->id])}}">{{$record->invoice_id}}  </a></td>
			                  <td>{{$record->email}}</td>
			                  @php
				                  $converted = optional($record->conversion)->status === 'converted';
			                  @endphp
			                  <td><span class="badge @if($converted)
						                  badge-success @else badge-warning @endif">@if($converted)
						                  Converted @else NA @endif</span></td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
	                {{$records->appends(request()->query())->links()}}
                </div>
            </div>
        </div>
    </div>
@endcomponent
