@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create New User</div>
                
                <div class="card-body">
	                {!! Form::open([
	                'url' => route("users.store"),
	                'method'=>"POST",
	                'class'=>"needs-validation", "novalidate",
	                ]) !!}
	                @include('admin.users._partials.form',['buttonText'=>"Create"])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection