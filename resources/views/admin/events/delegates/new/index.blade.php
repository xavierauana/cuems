@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Newly registered delegates for Event: {{$event->title}}
			        <a href="{{route('events.delegates.new.export', $event)}}"
			           class="btn btn-sm btn-outline-primary pull-right mr-1">Export</a>
			        <a href="{{route('events.delegates.new.import', $event)}}"
			           class="btn btn-sm btn-outline-primary pull-right mr-1">Update</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Institution</th>
                        <th>Role</th>
                        <th>Is Duplicated</th>
                    </thead>
	                  <tbody>
	                  @foreach($delegates as $delegate)
		                  <tr data-id="{{$delegate->id}}">
			                  <td><a target="_blank"
			                         href="{{route('events.delegates.show',[$event, $delegate])}}">{{$delegate->name}}</a></td>
			                  <td>{{$delegate->email}}</td>
			                  <td>{{$delegate->institution}}</td>
			                  <td>{{$delegate->roles->implode('label',', ')}}</td>
			                  <td class="duplicate">@if($delegate->isDuplicated())
					                  <span class="badge badge-warning">Duplicated</span>
				                  @else
					                  <span class="badge badge-success">NO</span>
				                  @endif</td>
			                  </td>
						  </tr>
	                  @endforeach
	                  </tbody>
	        </table>
                </div>
            </div>
	</div>
	</div>
	
	
	@push('scripts')
		
		<script>
		function toggleDuplicated(e, id) {
          e.target.disabled = true
          var url = "/events/{{$event->id}}/delegates/" + id + "/duplicated"
          axios.post(url)
               .then(function (response) {
                 e.target.disabled = false
                 if (response.data.status == 'completed') {
                   toggleBadge(id, response.data.duplicated)
                   toggleButton(id, response.data.duplicated)
                 }
               })
        }

        function toggleBadge(id, duplicate) {
          var tr   = document.querySelector("tr[data-id='" + id + "']"),
              span = tr.querySelector("td.duplicate span")

          if (duplicate) {
            span.innerText = "DUPLICATED"
            span.className = "badge badge-warning"
          } else {
            span.innerText = "NO"
            span.className = "badge badge-success"
          }
        }

        function toggleButton(id, duplicate) {
          var tr     = document.querySelector("tr[data-id='" + id + "']"),
              button = tr.querySelector("button.duplicate-button")

          if (duplicate) {
            button.innerText = "Not Duplicated"
            button.className = "duplicate-button btn btn-primary"
          } else {
            button.innerText = "Mark Duplicated"
            button.className = "duplicate-button btn btn-warning"
          }
        }
	</script>
	
	@endpush
@endcomponent
