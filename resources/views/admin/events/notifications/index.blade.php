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
                        <th>Role</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->notifications as $notification)
		                  <tr>
			                  <td>{{$notification->name}}</td>
			                  <td>{{$notification->eventName}}</td>
			                  <td>{{$notification->template}}</td>
			                  <td>{{optional($notification->role)->label}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route('events.notifications.edit',[$event, $notification])}}">Edit</a>
						                   <button class="btn btn-primary text-light"
						                           onclick="sendTest(event, {{$notification->id}})">Test</button>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   <form class="action"
						                         action="{{route("events.notifications.destroy",[$event, $notification])}}"
						                         onsubmit="deleteItem(event)"
						                         method="POST">
							                   @method('delete')
							                   @csrf
							                   <button type="submit"
							                           class="btn btn-sm btn-danger text-light">Delete</button>
						                   </form>
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
	
	<div id="test_notification" class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		  
	    <div class="modal-content">
		    <form class="form"
		          action="{{route("events.notifications.test", $event->id)}}"
		          method="POST">
			    {{csrf_field()}}
			    <div class="modal-header">
	        <h5 class="modal-title">Test Notification</h5>
	        <button type="button" class="close" data-dismiss="modal"
	                aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
			    <div class="modal-body">
				      
					     <div class="form-group">
						     <label class="form-label"
						            for="email">Test Email</label>
						     <input name="email"
						            id="email"
						            class="form-control"
						            type="email" />
					     </div>
				    
				    <input type="hidden" name="notification_id"
				           id="notification_id" />
			      </div>
			    <div class="modal-footer">
				    <button type="submit" class="btn btn-primary">Send</button>
				    <button type="button" class="btn btn-secondary"
				            data-dismiss="modal">Close</button>
			      </div>
			    </form>
	    </div>
	  </div>
	</div>
	
	@push('scripts')
		<script>
			function deleteItem(e) {
              e.preventDefault();
              if (confirm("are you sure to delete the notificaiton?")) {
                e.target.submit()
              }
            }

            function sendTest(e, notificationId) {
              e.preventDefault()
              var el = document.getElementById("test_notification")
              $(el).modal('show')
              el.querySelector('input#email').value = ""
              el.querySelector('input#notification_id').value = notificationId
            }
		</script>
	
	@endpush
@endcomponent
