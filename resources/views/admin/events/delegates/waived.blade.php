@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
	         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Waived Delegates for Event: {{$event->title}}</div>
		       
		        <div class="table-responsive">
	              <table class="table">
	                <thead>
	                    <th>
	                        <a href="{{sortUrl('registration_id')}}">
	                        Registration ID
	                        </a>
	                    </th>
	                    <th>
	                        <a href="{{sortUrl('first_name')}}">
	                        Name
	                        </a>
	                    </th>
	                    <th>
	                        <a href="{{sortUrl('email')}}">
		                        Email
	                        </a>
	                    </th>
	                    <th>
	                        <a href="{{sortUrl('institution')}}">
		                        Institution
	                        </a>
	                    </th>
	                    <th>Role</th>
	                    <th>Actions</th>
	                </thead>
	                  <tbody>
	                  @foreach($delegates as $delegate)
		                  <tr>
			                  <td>{{$delegate->getRegistrationId()}}</td>
			                  <td><a href="{{route('events.delegates.show',[$event,$delegate])}}">{{$delegate->name}}</a></td>
			                  <td>{{$delegate->email}}</td>
			                  <td>{{$delegate->institution}}</td>
			                  <td>{{$delegate->roles->implode('label',', ')}}</td>
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
	                <div class="col">
		               
		            {!! $delegates->appends(request()->query())->links() !!}
	                </div>
	                
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