@extends("layouts.app")

@section("content")
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	            <div class="card">
	                <div class="card-header">
		                <h2 class="d-inline-block m-0">Create new upload file</h2>
	                </div>
		            <div class="card-body">
			            {{Form::open(['url'=>route("events.uploadFiles.store",$event),'method'=>'POST','files'=>true])}}
			            @include('admin.events.uploadFiles.form.default', ['buttonText'=>'Create'])
			            {{Form::close()}}
		            </div>
	            </div>
	        </div>
	    </div>
	</div>
@endsection