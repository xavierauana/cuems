@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header">Talk for Session: {{$session->title}}
			        <a href="{{route('events.sessions.talks.create', [$event, $session])}}"
			           class="btn btn-sm btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i>

						        New</a>
                </div>
                
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                        <th>Title</th>
                        <th>Actions</th>
                    </thead>
	                  <tbody>
	                  @foreach($session->talks as $talk)
		                  <tr>
			                  <td>{{$talk->title}}</td>
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
