@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Notification for Event: {{$event->title}}
			        <a href="{{route('events.notifications.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
			        <a href="{{route('events.notifications.import', $event)}}"
			           class="btn btn-sm btn-outline-success pull-right mr-1">Upload Template</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Event</th>
                        <th>Template</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->notifications as $notification)
		                  <tr>
			                  <td>{{$notification->name}}</td>
			                  <td>{{$notification->eventName}}</td>
			                  <td>{{$notification->template}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route('events.notifications.edit',[$event, $notification])}}">Edit</a>
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
