@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Advertisements for Event: {{$event->title}}
			        <a href="{{route('events.advertisements.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Buyer</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($advertisements as $advertisement)
		                  <tr>
			                  <td>{{$advertisement->buyer}}</td>
			                  <td>{{$advertisement->type->name}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a href="{{route('events.advertisements.edit', [$event, $advertisement])}}"
						                      class="btn btn-info text-light">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   <form class="form"
						                         method="POST"
						                         action="{{route("events.advertisements.destroy",[$event, $advertisement])}}"
						                         @submit.prevent="confirmDelete"
						                         style="display: inline">
							                   @csrf
							                   <input name="_method"
							                          value="DELETE"
							                          type="hidden" />
							                   <button class="btn btn-danger btn-sm text-light">Delete</button>
						                   </form>
									  </div>
				                  </div>
			                  </td>
		                  </tr>
	                  @endforeach
	                  </tbody>
                  </table>
	                {{$advertisements->links()}}
                </div>
            </div>
        </div>
    </div>
@endcomponent
