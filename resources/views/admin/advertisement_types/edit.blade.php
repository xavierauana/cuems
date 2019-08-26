@extends('layouts.app')

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	             @include("admin._partials.alert")
		        <div class="card">
	                <div class="card-header">Edit Advertisement Type</div>
	                
	                <div class="card-body">
		                
		                {!! Form::model($advertisementType,['url' => route("advertisement_types.update", $advertisementType), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
		
		                @include("admin.advertisement_types._partials.form",['buttonText'=>'Update'])
		
		                {!! Form::close() !!}
		                
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	@push('scripts')
		@include("_partials.ckeditor_script")
	@endpush
@endsection