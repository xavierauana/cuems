@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
	         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Delegates for Event: {{$event->title}}
			        <a href="{{route('events.delegates.new', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
			        {{--<a href="{{route('events.delegates.create', $event)}}"--}}
			           {{--class="btn btn-sm btn-success pull-right">New</a>--}}
			        {{--<a href="{{route('delegates.import_template')}}"--}}
			           {{--target="_blank"--}}
			           {{--class="btn btn-sm btn-outline-primary pull-right mr-1">Download Import Template</a>--}}
			        {{--<a href="{{route('events.delegates.import', $event)}}"--}}
			           {{--class="btn btn-sm btn-outline-success pull-right mr-1">Import</a>--}}
			        <a href="{{route('events.delegates.export', $event)}}"
			           class="btn btn-sm btn-outline-primary pull-right mr-1">Export</a>
			        <a href="{{route('events.delegates.duplicates', $event)}}"
			           class="btn btn-sm btn-warning pull-right mr-1">Duplicates</a>
	            </div>
		        <div class="card-body">
			        <div class="row">
				        <div class="col-6">
					        <form class="form"
					              action="{{route('events.delegates.index', $event)}}">
						        <div class="input-group">
								    <input class="form-control"
								           name="keyword"
								           value="{{request()->query('keyword')}}"
								           placeholder="search" />
								        <div class="">
									        <button class="btn btn-success">Search</button>
								        </div>
							        </div>
					        </form>
				        </div>
			        </div>
		        </div>
	            <div class="table-responsive">
	              <table class="table">
	                <thead>
	                    <th>
	                        <a href="{{request()->url()."?registration_id=".((request()->has('registration_id') and request()->query('registration_id') === 'asc')?"desc":"asc")}}">
	                        Registration ID
	                        </a>
	                    </th>
	                    <th>
	                        <a href="{{request()->url()."?first_name=".((request()->has('first_name') and request()->query('first_name') === 'asc')?"desc":"asc")}}">
	                        Name
	                        </a>
	                    </th>
	                    <th>
	                        <a href="{{request()->url()."?last_name=".((request()->has('last_name') and request()->query('last_name') === 'asc')?"desc":"asc")}}">
		                        Email
	                        </a>
	                    </th>
	                    <th>
	                        <a href="{{request()->url()."?institution=".((request()->has('institution') and request()->query('institution') === 'asc')?"desc":"asc")}}">
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