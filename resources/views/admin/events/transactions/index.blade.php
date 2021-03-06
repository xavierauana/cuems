<?php
$status = array_flip((new \ReflectionClass(\App\Enums\TransactionStatus::class))->getConstants());
?>
@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Delegates for Event: {{$event->title}}</div>
                
		        <div class="row mt-3 mx-3">
			        <div class="col-md-6 float-right">
				        <form action="{{route('events.transactions.search', $event)}}"
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
                        <th>
	                        <a href="{{sortUrl('d.registration_id')}}">
		                        Registration Id
	                        </a>
                        </th>
                        <th>
	                        <a href="{{sortUrl('d.first_name')}}">
		                        Delegate Name
	                        </a>
                        </th>
                        <th>
	                         <a href="{{sortUrl('t.name') }}">
		                       Ticket
	                        </a>
                        </th>
                        <th>
	                        <a href="{{sortUrl("transactions.charge_id")}}">
		                       Transaction Id
	                        </a>
                        </th>
                        <th>
	                        <a href="{{sortUrl("transactions.created_at")}}">
		                       Timestamp
	                        </a>
                        </th>
                        <th>
	                        <a href="{{sortUrl("transactions.status")}}">
		                       Status
	                        </a>
                        </th>
                    </thead>
	                  <tbody>
	                  @foreach($transactions as $transaction)
		                  <tr>
			                  <td>
				                  @if($delegate = $transaction->payee)
					                  {{$delegate->getRegistrationId()}}
				                  @else
					                  NA
				                  @endif
			                  </td>
			
			                  <td>@if($delegate) <a
						                  href="{{route('events.delegates.show',[$event,$delegate])}}">{{$delegate->name}}</a> @else
					                  NA @endif</td>
			                  <td>{{$transaction->ticket->name}}</td>
			                  <td>{{$transaction->charge_id}}</td>
			                  <td>{{$transaction->created_at->toDateTimeString()}}</td>
			                  <td>{{$status[$transaction->status]}}</td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
	                {{$transactions->appends(request()->query())->links()}}
                </div>
            </div>
        </div>
    </div>
@endcomponent
