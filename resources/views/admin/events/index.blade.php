@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
         @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Events
                <a href="{{route('events.create')}}"
                   class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Event Title</th>
                        <th>Start At</th>
                        <th>End At</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($events as $event)
		                  <tr>
			                  <td>{{$event->title}}</td>
			                  <td>{{$event->start_at->format("d M Y")}}</td>
			                  <td>{{$event->end_at->format("d M Y")}}</td>
			                  <td>
				                  <div class="btn-toolbar" role="toolbar"
				                       aria-label="Toolbar with button groups">
					                   <div class="btn-group  btn-group-sm mr-2"
					                        role="group"
					                        aria-label="First group">
						                   <a href="{{route('events.details', $event)}}"
						                      class="btn btn-primary">Details</a>
						                   <a class="btn btn-info text-light">Edit</a>
									  </div>
					                   <div class="btn-group btn-group-sm mr-2"
					                        role="group"
					                        aria-label="Second group">
						                  <button class="btn btn-danger text-light">Delete</button>
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
</div>
@endsection
