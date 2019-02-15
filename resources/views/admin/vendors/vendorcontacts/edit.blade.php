@extends("layouts.app")

@section("content")
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	            <div class="card">
	                <div class="card-header">
		                <h2 class="d-inline-block m-0">Edit vendorContact</h2>
	                </div>
		            <div class="card-body">
			            {{Form::model($vendorContact,['url'=>route("vendors.vendorContacts.update",[$vendor,$vendorContact]),'method'=>'PUT'])}}
			            @include('admin.vendors.vendorContacts.form.default', ['buttonText'=>'Edit'])
			            {{Form::close()}}
		            </div>
	            </div>
	        </div>
	    </div>
	</div>
@endsection