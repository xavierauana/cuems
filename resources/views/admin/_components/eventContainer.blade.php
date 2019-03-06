@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row">
	    <div class="col-12 col-sm-2 sidebar">
			<ul class="nav flex-column">
				<li class="nav-item dropdown">
					<a class="nav-link"
					   href="{{route('events.details', $event)}}">Dashboard</a>
				</li>
				<li class="nav-item dropdown">
				    <a class="nav-link dropdown-toggle" data-toggle="dropdown"
				       href="#" role="button" aria-haspopup="true"
				       aria-expanded="false">Conference Info</a>
				    <div class="dropdown-menu">
					    <a class="dropdown-item"
					       href="{{route('events.delegates.index', $event)}}">Delegates</a>
					    <a class="dropdown-item"
					       href="{{route('events.sessions.index', $event)}}">Sessions</a>
					    <a class="dropdown-item"
					       href="{{route('events.sponsors.index', $event)}}">Sponsors</a>
					    <a class="dropdown-item"
					       href="{{route('events.tickets.index', $event)}}">Tickets</a>
				    </div>
				  </li>
				<li class="nav-item dropdown">
				    <a class="nav-link dropdown-toggle" data-toggle="dropdown"
				       href="#" role="button" aria-haspopup="true"
				       aria-expanded="false">Notifications</a>
				    <div class="dropdown-menu">
					    <a class="dropdown-item"
					       href="{{route('events.notifications.index', $event)}}">Notifications</a>
					   <a class="dropdown-item"
					      href="{{route('events.uploadFiles.index', $event)}}">Attachments</a>
				    </div>
				  </li>
				<li class="nav-item dropdown">
				    <a class="nav-link dropdown-toggle" data-toggle="dropdown"
				       href="#" role="button" aria-haspopup="true"
				       aria-expanded="false">Transactions</a>
				    <div class="dropdown-menu">
					    <a class="dropdown-item"
					       href="{{route('events.transactions.index', $event)}}">Success</a>
					   <a class="dropdown-item"
					      href="{{route('events.payment_records.index', $event)}}">Fail</a>
				    </div>
				  </li>
				<li class="nav-item dropdown">
				    <a class="nav-link dropdown-toggle" data-toggle="dropdown"
				       href="#" role="button" aria-haspopup="true"
				       aria-expanded="false">Settings</a>
				    <div class="dropdown-menu">
					    <a class="dropdown-item"
					       href="{{route('events.settings.index',$event)}}">Key</a>
					   <a class="dropdown-item"
					      href="{{route('events.expenses.index', $event)}}">Expenses</a>
				    </div>
				  </li>
				<li class="nav-item dropdown">
					<a class="nav-link"
					   href="{{route('events.checkin.index', $event)}}">Check In</a>
				</li>
			</ul>
	    </div>
	    <div class="col-12 col-sm-10">
		    {{$slot}}
	    </div>
    </div>
</div>
@endsection
