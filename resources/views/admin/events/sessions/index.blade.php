@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Sessions for Event: {{$event->title}}
			        <a href="{{route('events.sessions.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Duration</th>
                        <th>Moderation Type</th>
                        <th>Moderators</th>
                        <th>Number of talks</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->sessions as $session)
		                  <tr>
			                  <td>{{$session->title}}</td>
			                  <td>{{$session->subtitle}}</td>
			                  <td>{{$session->duration}}</td>
			                  <td>{{title_case(\App\Enums\SessionModerationType::getType($session->moderation_type))}}</td>
			                  <td>
				                  @foreach($session->moderatorDelegates as $delegate)
					                  <a href="{{route('events.delegates.show',[$event,$delegate])}}"><span
								                  class="badge badge-success">{{$delegate->name}}</span></a>
				                  @endforeach
			                  </td>
			                  <td>{{$session->talks->count()}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a href="{{route('events.sessions.talks.index', [$event,$session])}}"
						                      class="btn btn-primary">Talks</a>
						                   <a href="{{route('events.sessions.edit', [$event, $session])}}"
						                      class="btn btn-info text-light">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   <form class="form"
						                         method="POST"
						                         action="{{route("events.sessions.destroy",[$event, $session])}}"
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
                </div>
            </div>
        </div>
    </div>
@endcomponent
