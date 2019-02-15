@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">
			        Delegates Sponsors: {{$sponsor->name}}
			        <a class="btn btn-outline-primary text-primary btn-sm float-right"
			           href="{{route("events.sponsors.delegates.export", [$event, $sponsor])}}">Export</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Registration Id</th>
                        <th>Name</th>
                        <th>Email</th>
                    </thead>
	                  <tbody>
	                  @foreach($sponsor->delegates as $delegate)
		                  <tr>
			                  <td><a href="{{route('events.delegates.show',[$event,$delegate])}}">{{$delegate->getRegistrationId()}}</a></td>
			                  <td>{{$delegate->name}}</td>
			                  <td>{{$delegate->email}}</td>
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

