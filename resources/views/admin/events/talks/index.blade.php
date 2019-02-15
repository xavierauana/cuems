@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Talks for Event: {{$event->title}}</div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Title</th>
                        <th>Session</th>
                    </thead>
	                  <tbody>
	                  @foreach($talks as $talk)
		                  <tr>
			                  <td><a href="{{route("events.sessions.talks.edit", [$event, $talk->session, $talk])}}">{{$talk->title}}</a> </td>
			                  <td>{{$talk->session->title}}</td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent

