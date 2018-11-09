@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create new vendor</div>
                
                <div class="card-body">
	                {!! Form::open([
	                'url' => route("vendors.store"),
	                'method'=>"POST",
	                'class'=>"needs-validation", "novalidate",
	                ]) !!}
	                @include('admin.vendors._partials.form',['buttonText'=>"Create"])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection