@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
			                  <td>{{$transaction->card_brand}}</td>
			                  <td>{{$transaction->last_4}}</td>
			                  <td>{{$transaction->created_at->toDateTimeString()}}</td>
			                  <td>{{$transaction->status}}</td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
