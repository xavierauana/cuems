@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Expenses for Event: {{$event->title}}
			        <a href="{{route('events.expenses.create', $event)}}"
			           class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($event->expenses as $expense)
		                  <tr>
			                  <td>{{number_format($expense->amount)}}</td>
			                  <td>{{$expense->categoryName }}</td>
			                  <td>{{$expense->vendorName }}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a class="btn btn-info text-light"
						                      href="{{route('events.expenses.edit',[$event, $expense])}}">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                   {{Form::open(['url'=>route('events.expenses.destroy',[$event, $expense]),'method'=>'DELETE','onsubmit'=>"confirmDelete(event, '{$expense->name}')"])}}
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

