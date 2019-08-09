@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
	         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Position Grouping for Event: {{$event->title}}
		                                 (Total: {{$groupings->total()}})
			        {{--			        <a href="{{route('events.position-groupings.new', $event)}}"--}}
			        {{--			           class="btn btn-sm btn-success pull-right">New</a>--}}
			        <a href="{{route('events.position-groupings.export', $event)}}"
			           class="btn btn-sm btn-outline-primary pull-right mr-1">Export</a>
			        			        <a href="{{route('events.position-groupings.import', $event)}}"
						                   class="btn btn-sm btn-outline-success pull-right mr-1">Import</a>
	            </div>
		        <div class="card-body">
			        <div class="row">
				        <div class="col-6">
					        <form class="form"
					              action="{{route('events.position-groupings.index', $event)}}">
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
				                <a href="{{sortUrl('position')}}">
					                Position
				                </a>
	                        </th>
		                    <th>
		                        <a href="{{sortUrl('grouping')}}">
		                        Grouping
		                        </a>
		                    </th>
		                    <th>Actions</th>
	                    </thead>
			            <tbody>
			            @foreach($groupings as $grouping)
				            <tr>
					            <td>{{$grouping->position}}</td>
					            <td>{{$grouping->grouping}}</td>
					            <td>
						            <div class="btn-toolbar" role="toolbar"
						                 aria-label="Toolbar with button groups">
							            <div class="btn-group  btn-group-sm mr-2"
							                 role="group"
							                 aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route("events.delegates.edit",[$event, $grouping])}}">Edit</a>
									    </div>
							            <form class="action"
							                  action="{{route("events.delegates.destroy",[$event, $grouping])}}"
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
		               
		            {!! $groupings->appends(request()->query())->links() !!}
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