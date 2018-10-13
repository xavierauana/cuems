@component('admin._components.eventContainer', ['event'=>$event])
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
@endcomponent