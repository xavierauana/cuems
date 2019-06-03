@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
	         @include("admin._partials.alert")
	        <div class="card mb-3">
		        <div class="card-header">Stats</div>
		        <div class="card-body">
			        <div class="row">
				        @foreach($stats as $date=>$stat)
					        <div class="
					        @switch(count($stats))
					        @case(1) col-sm-12 @break
					        @case(2) col-sm-6 @break
					        @case(3) col-sm-4 @break
					        @default col-sm-3
@endswitch
							        ">
						        <div class="card mb-3">
							        <div class="card-header">{{$date}}</div>
							        <div class="card-body"><h3><a
											        href="{{route('events.checkinRecords',[$event,'keyword'=>request('keyword'),'date'=>$date])}}">{{number_format($stat)}}</a></h3></div>
						        </div>
					        </div>
				        @endforeach
			        </div>
			        
		        </div>
	        </div>
	        <div class="card">
		        <div class="card-header d-flex">
			        <p class="d-inline-block m-0">
				        Check In Records for Event: {{$event->title}}
				        (Total: {{$records->total()}})
			        </p>
			        <a href="{{route('events.checkinRecords.export', [$event]+request()->query())}}"
			           class="btn btn-success btn-sm text-light ml-auto d-inline-block mr-1">Export</a>
			        <button type="button" class="btn btn-primary btn-sm"
			                data-toggle="modal"
			                data-target="#create_notification  ">Create Notification</button>
		        </div>
		        <div class="card-body">
			        <div class="row">
				        <div class="col-6">
					        <form class="form"
					              action="{{route('events.checkinRecords', $event)}}">
						        <div class="input-group">
								    <input type="hidden"
								           name="date"
								           value="{{request()->query('date')}}" />
								    <input class="form-control"
								           name="keyword"
								           value="{{request()->query('keyword')}}"
								           placeholder="search" />
								        <div class="">
									        <button class="btn btn-success">Search</button>
									        <a href="{{route('events.checkinRecords', $event)}}"
									           class="btn btn-info text-light">Reset</a>
								        </div>
							        </div>
					        </form>
				        </div>
			        </div>
		        </div>
	            <div class="table-responsive">
		            <table class="table">
			            <thead>
			                <tr>
				                <th>Registration Id</th>
					            <th>Delegate Name</th>
					            <th>Ticket Name</th>
					            <th>Check In Time</th>
			                </tr>
	                    </thead>
			            <tbody>
			            @foreach($records as $record)
				            <tr>
					            <td>{{(setting($event, 'registration_id_prefix') ?? "") . str_pad($record->registration_id, 4, 0,STR_PAD_LEFT)}}</td>
					            <td>
						            <a href="{{route('events.delegates.show', [$event, $record->delegate_id])}}">
							            {{$record->first_name}} {{$record->last_name}}
						            </a>
					            </td>
					            <td>{{$record->ticket_name}}</td>
					            <td>{{$record->created_at}}</td>
		                  </tr>
			            @endforeach
			            </tbody>
	              </table>
	                <div class="col">
		                {!! $records->appends(request()->query())->links() !!}
	                </div>
	            </div>
	        </div>
        </div>
    </div>
	
	<!-- Modal -->
	<div class="modal fade" id="create_notification" tabindex="-1"
	     role="dialog"
	     aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog" role="document">
			{{Form::open(['url'=>route('events.notifications.store', $event), 'method'=>'POST'])}}
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Create Notification</h5>
						<button type="button" class="close" data-dismiss="modal"
						        aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
				</div>
				<div class="modal-body">
					@include('admin.events.checkinRecords.form.create_notification')
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary"
					        data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
				{{Form::close()}}
			</div>
		</div>
	</div>
	@push('styles')
		<link rel="stylesheet"
		      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	@endpush
	
	@push('scripts')
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
		
		<script>
		_.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, {
          enableTime: true,
          dateFormat: "d M Y H:i",
        }))
	</script>
	@endpush

@endcomponent

