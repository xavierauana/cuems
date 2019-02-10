@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Sponsors for Event: {{$event->title}}
			        <a href="{{route('events.sponsors.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
			        <a href="{{route('events.sponsors.import', $event)}}"
			           class="btn btn-sm btn-outline-success pull-right mr-1">Import</a>
			        <a href="{{route('sponsors.download_template')}}"
			           target="_blank"
			           class="btn btn-sm btn-outline-primary pull-right mr-1">Download Sponsor Template</a>
			        <a href="{{route('events.sponsors.delegates.export.all',$event)}}"
			           target="_blank"
			           class="btn btn-sm btn-outline-primary pull-right mr-1">Export Delegates</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->sponsors as $sponsor)
		                  <tr>
			                  <td>{{$sponsor->name}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route('events.sponsors.edit',[$event, $sponsor])}}">Edit</a>
						                   <a class="btn btn-primary text-light"
						                      href="{{route('events.sponsors.delegates',[$event, $sponsor])}}">Delegates</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   {{Form::open(['url'=>route('events.sponsors.destroy',[$event, $sponsor]),'method'=>'DELETE','onsubmit'=>"confirmDelete(event, '{$sponsor->name}')"])}}
						                   <button class="btn btn-danger btn-sm text-light"
						                           type="submit">Delete</button>
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

