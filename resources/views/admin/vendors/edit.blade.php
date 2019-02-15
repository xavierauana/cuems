@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit vendor: {{$vendor->name}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($vendor, ['url' => route("vendors.update", $vendor), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	                @include('admin.vendors._partials.form', ['buttonText'=>'Update'])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection