@component('admin._components.eventContainer', ['event'=>$event])
	<div class="card">
	    <div class="card-body">
		    <div class="row">
			    <div class="col-12">
				    <h3>Delegates</h3>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.index", $event)}}"><h4
								    class="card-header">Total</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.new", $event)}}"><h4
								    class="card-header">New</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->whereIsVerified(false)->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.duplicates", $event)}}"><h4
								    class="card-header">Duplicated</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->whereIsDuplicated(\App\Enums\DelegateDuplicationStatus::DUPLICATED)->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.sponsored", $event)}}"><h4
								    class="card-header">Sponsored</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->sponsored()->count())}}</h5>
					    </div>
				    </div>
			    </div>
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route("events.delegates.waived", $event)}}"><h4
								    class="card-header">Waived</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->delegates()->waived()->count())}}</h5>
					    </div>
				    </div>
			    </div>
{{--			    <div class="mb-3 col-sm-6 col-md-4">--}}
{{--				    <div class="card">--}}
{{--					    <a href="{{route("events.tickets.index", $event)}}"><h4--}}
{{--								    class="card-header">Tickets</h4></a>--}}
{{--					    <div class="card-body">--}}
{{--						    <h5>{{number_format($event->tickets()->count())}}</h5>--}}
{{--					    </div>--}}
{{--				    </div>--}}
{{--			    </div>--}}
			
			    <div class="mb-3 col-sm-6 col-md-4">
				    <div class="card">
					    <a href="{{route('events.checkinRecords', $event)}}"><h4
								    class="card-header">Check In</h4></a>
					    <div class="card-body">
						    <h5>{{number_format($event->getCheckInCount())}}</h5>
					    </div>
				    </div>
			    </div>
		    </div>
		    <div class="row">
			    <div class="col-12">
				    <h3>Scientific Program</h3>
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
			    					    <a href="{{route('events.talks.all', $event)}}"><h4
											        class="card-header">Talks</h4></a>
			    					    <div class="card-body">
			    						    <h5>{{number_format($event->sessions->reduce(function(int $carry, \App\Session $session){
			    						    return $carry + $session->talks()->count();
			    						    },0))}}</h5>
			    					    </div>
			    				    </div>
			    			    </div>
		    </div>
		    
	    </div>
	</div>
@endcomponent
