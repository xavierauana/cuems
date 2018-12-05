@component('admin._components.eventContainer', ['event'=>$event])
	<div class="card">
	    <div class="card-body">
		    <div class="row">
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.index", $event)}}"><h4
								    class="card-header">Total Delegates</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route('events.sessions.index', $event)}}"><h4
								    class="card-header">Sessions</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->sessions()->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route('events.tickets.index', $event)}}"><h4
								    class="card-header">Tickets</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->tickets()->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.new", $event)}}"><h4
								    class="card-header">Newly Added Delegates</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->whereIsVerified(false)->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    
		    </div>
		    
	    </div>
	</div>
@endcomponent