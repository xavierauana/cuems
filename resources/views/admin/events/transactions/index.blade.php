<?php
$status = array_flip((new \ReflectionClass(\App\Enums\TransactionStatus::class))->getConstants());
?>
@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Delegates for Event: {{$event->title}}</div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Delegate Name</th>
                        <th>Ticket</th>
                        <th>Card</th>
                        <th>Last 4</th>
                        <th>Date</th>
                        <th>Status</th>
                    </thead>
	                  <tbody>
	                  @foreach($transactions as $transaction)
		                  <tr>
			                  <td>{{$transaction->payee->name}}</td>
			                  <td>{{$transaction->ticket->name}}</td>
			                  <td>{{$transaction->card_brand ?? "NA"}}</td>
			                  <td>{{$transaction->last_4 ?? "NA"}}</td>
			                  <td>{{$transaction->created_at->toDateTimeString()}}</td>
			                  <td>{{$status[$transaction->status]}}</td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
