@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Delegates for Event: {{$event->title}}
			        <a href="{{route('events.delegates.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
			        {{--<a href="{{route('events.delegates.import', $event)}}"--}}
			           {{--class="btn btn-sm btn-outline-success pull-right mr-3">Import</a>--}}
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Institution</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($delegates as $delegate)
		                  <tr>
			                  <td>{{$delegate->name}}</td>
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