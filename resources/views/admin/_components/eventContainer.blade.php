@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row">
	    <div class="col-sm-2 sidebar">
			<ul class="nav flex-column">
				<li class="nav-item dropdown">
					<a class="nav-link"
					   href="{{route('events.details', $event)}}">Dashboard</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link"
					   href="{{route('events.sessions.index', $event)}}">Sessions</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.delegates.index', $event)}}">Delegates</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.tickets.index', $event)}}">Tickets</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.sponsors.index', $event)}}">Sponsors</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.transactions.index', $event)}}">Transactions</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.payment_records.index', $event)}}">Payment Records</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.notifications.index', $event)}}">Notifications</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.uploadFiles.index', $event)}}">Upload Files</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.expenses.index', $event)}}">Expenses</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.settings.index',$event)}}">Settings</a>
				</li>
			</ul>
	    </div>
	    <div class="col">
		    {{$slot}}
	    </div>
    </div>
</div>
@endsection
