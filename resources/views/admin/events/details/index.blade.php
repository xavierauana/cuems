@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row">
	    @include("admin._partials.alert")
	    <div class="col-sm-2 sidebar">
			<ul class="nav flex-column">
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
			</ul>
	    </div>
	    <div class="col">
		    <div class="card">
			    <div class="card-body">
				    <div class="row">
					    <div class="col-sm-6 col-md-4">
						    <div class="card">
							    <a href="{{route("events.delegates.index", $event)}}"><h4
										    class="card-header">Delegates</h4></a>
							    <div class="card-body">
								    <h5>{{number_format($event->delegates()->count())}}</h5>
							    </div>
						    </div>
					    </div>
					    <div class="col-sm-6 col-md-4">
						    <div class="card">
							    <a href="{{route('events.sessions.index', $event)}}"><h4
										    class="card-header">Sessions</h4></a>
							    <div class="card-body">
								    <h5>{{number_format($event->sessions()->count())}}</h5>
							    </div>
						    </div>
					    </div>
					    <div class="col-sm-6 col-md-4">
						    <div class="card">
							    <a href="{{route('events.tickets.index', $event)}}"><h4
										    class="card-header">Tickets</h4></a>
							    <div class="card-body">
								    <h5>{{number_format($event->tickets()->count())}}</h5>
							    </div>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
	    </div>
	    
    </div>
</div>
@endsection
