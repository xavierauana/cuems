@extends("layouts.app")

@section("content")
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	            <div class="card">
	                <div class="card-header">
		                <h2 class="d-inline-block m-0">Edit uploadFile</h2>
	                </div>
		            <div class="card-body">
			            {{Form::model($uploadFile,['url'=>route("events.uploadFiles.update",[$event,$uploadFile]),'method'=>'PUT','files'=>true])}}
			            @include('admin.events.uploadFiles.form.default', ['buttonText'=>'Edit'])
			            {{Form::close()}}
		            </div>
	            </div>
	        </div>
	    </div>
	</div>
@endsection