@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Tickets for Event: {{$event->title}}
			        <a href="{{route('events.tickets.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
			        <a href="{{route('events.tickets.import', $event)}}"
			           class="btn btn-sm btn-outline-success pull-right mr-3">Import</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Vacancy</th>
                        <th>Duration</th>
                        <th>Is Public</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->tickets as $ticket)
		                  <tr>
			                  <td>{{$ticket->name}}</td>
			                  <td>{{"HK$". number_format($ticket->price, 1) }}</td>
			                  <td>{{$ticket->vacancy === null? "Unlimited" : $ticket->vacancy}}</td>
			                  <td>{{sprintf("%s - %s", $ticket->start_at->format('d M Y h:i A'), $ticket->end_at->format('d M Y h:i A') )}}</td>
			                  <td>{{$ticket->is_public?"Yes":"No"}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route('events.tickets.edit',[$event, $ticket])}}">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   {{Form::open(['url'=>route('events.tickets.destroy',[$event, $ticket]),'method'=>'DELETE','onsubmit'=>"confirmDelete(event, '{$ticket->name}')"])}}
						                   <button class="btn btn-danger btn-sm text-light" type="submit">Delete</button>
						                   {{Form::close()}}
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
	
	@push("scripts")
		<script>
			function confirmDelete(e, itemName) {
			  console.log(e)
              e.preventDefault()
              if (confirm(`Are you sure you wan to delete ${itemName}?`)) {
                e.target.submit()
              }
            }
		</script>
	@endpush
@endcomponent

