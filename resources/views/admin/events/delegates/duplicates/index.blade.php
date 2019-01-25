@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row">
			<div class="col">
				@include("admin._partials.alert")
				<div class="card">
			        <div class="card-header">Duplicated delegates for Event: {{$event->title}}
				        <a href="{{route('events.delegates.export', [$event,'duplicated'=>true])}}"
				           class="btn btn-sm btn-outline-primary pull-right mr-1">Export</a>
	                </div>
                <div class="table-responsive">
                  <table class="table">
	                  <thead>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Ticket</th>
                        <th>Transaction ID</th>
                        <th>Transaction Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($delegates as $delegate)
		                  <tr>
			                  <td><a href="{{route('events.delegates.show',[$event,$delegate])}}">{{$delegate->name}}</a> </td>
			                  <td>{{$delegate->email}}</td>
			                  <td>{{$delegate->mobile}}</td>
			                  <td>{{$delegate->transactions->first()->ticket->name}}</td>
			                  <td>{{$delegate->transactions->first()->charge_id ?? "NA"}}</td>
			                  <td>{{array_flip(\App\Enums\TransactionStatus::getStatus())[$delegate->transactions->first()->status]}}</td>
			                    <td>{{$delegate->created_at->toDatetimeString()}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route("events.delegates.edit",[$event, $delegate])}}">Edit</a>
									  </div>
					                    <form class="action"
					                          action="{{route("events.delegates.destroy",[$event, $delegate])}}"
					                          onsubmit="deleteItem(event)"
					                          method="POST">
							                   @method('delete')
						                    @csrf
						                    <button class="btn btn-sm btn-danger text-light">Delete</button>
						                   </form>
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
	
	@push('scripts')
		<script>
			function deleteItem(e) {
              e.preventDefault();
              if (confirm("are you sure to delete the delegate?")) {
                e.target.submit()
              }
            }
		</script>
	
	@endpush
@endcomponent