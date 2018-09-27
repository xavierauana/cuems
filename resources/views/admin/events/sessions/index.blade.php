@component('_components.eventContainer', ['event'=>$event])
    <div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Sessions for Event: {{$event->title}}
			        <a href="{{route('events.sessions.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Duration</th>
                        <th>Number of talks</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->sessions as $session)
		                  <tr>
			                  <td>{{$session->title}}</td>
			                  <td>{{$session->subtitle}}</td>
			                  <td>{{$session->duration}}</td>
			                  <td>{{$session->talks->count()}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a href="{{route('events.sessions.talks.index', [$event,$session])}}"
						                      class="btn btn-primary">Talks</a>
						                   <a href="{{route('events.sessions.edit', [$event, $session])}}"
						                      class="btn btn-info text-light">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                  <button class="btn btn-danger text-light">Delete</button>
									  </div>
				                  </div>
				                  
				                  
			                  </td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
