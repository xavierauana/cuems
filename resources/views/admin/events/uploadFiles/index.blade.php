@extends("layouts.app")

@section("content")
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
                @if (session('status'))
			        <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
		        @endif
		        <div class="card">
	                <div class="card-header">
		                <h2 class="d-inline-block m-0">Upload Files</h2>
		                <a class="mt-1 pull-right btn btn-sm btn-success"
		                   href="{{{route('events.uploadFiles.create',$event)}}}">
			                <i class="fa fa-plus-circle"
			                   aria-hidden="true"></i>
						</a>
	                </div>
		            
	                <div class="card-body">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                {{Form::open(['url'=>route('events.uploadFiles.index', $event),'method'=>'get'])}}
	                            <div class="input-group mb-3">
                                    <input type="text"
                                           name="keyword"
                                           class="form-control"
                                           placeholder="Search"
                                           aria-label="Search"
                                           aria-describedby="searchInput">
                                  <div class="input-group-append">
                                    <button class="btn btn-outline-secondary"
                                            type="submit">Find</button>
                                      <a href="{{route("events.uploadFiles.index", $event)}}"
                                         class="btn btn-outline-secondary">Clear</a>
                                  </div>
                                </div>
	                            {{Form::close()}}
                            </div>
                        </div>
                        <table class="table hover">
                           <thead>
                               <th>File Name</th>
                               <th>Actions</th>
                           </thead>
                            <tbody>
                                @foreach($uploadFiles as $uploadFile)
	                                <tr>
		                                <td>{{$uploadFile->name}}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a class="btn btn-info btn-sm text-light"
                                                   href="{{route('events.uploadFiles.edit',[$event,$uploadFile])}}">Edit</a>
                                            </div>
	
	                                        {{Form::open(['url'=>route('events.uploadFiles.destroy',[$event,$uploadFile]),'method'=>"DELETE",'style'=>'display:inline','@submit.prevent'=>"confirmDelete"])}}
	                                        <button class="btn btn-danger btn-sm"
	                                                type="submit">Delete</button>
	                                        {{Form::close()}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
		                {{ $uploadFiles->links() }}
                    </div>
	            </div>
	        </div>
	    </div>
	</div>
@endsection