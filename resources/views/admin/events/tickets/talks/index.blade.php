@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Talk for Session: {{$session->title}}
			        <a href="{{route('events.sessions.talks.create', [$event, $session])}}"
			           class="btn btn-sm btn-success pull-right"><i
						        class="fa fa-plus" aria-hidden="true"></i>

						        New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Speakers</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($session->talks->sortBy('order') as $talk)
		                  <tr>
			                  <td>{{$talk->order}}</td>
			                  <td>{{$talk->title}}</td>
			                  <td>
				                  @foreach($talk->speakerDelegates as $delegate)
					                  <a href="{{route('events.delegates.show',[$event, $delegate])}}"><span
								                  class="badge badge-success">{{$delegate->name}}</span></a>
				                  @endforeach
			                  </td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a href="{{route('events.sessions.talks.edit', [$event, $session, $talk])}}"
						                      class="btn btn-info text-light">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   <form method="POST"
						                         style="display: inline-block"
						                         action="{{route("events.sessions.talks.destroy",[$event, $session, $talk])}}"
						                         @submit.prevent="confirmDelete">
							                   <input name="_method"
							                          type="hidden"
							                          value="DELETE" />
							                   @csrf
							                   <button class="btn btn-danger btn-sm text-light">Delete</button>
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
@endcomponent