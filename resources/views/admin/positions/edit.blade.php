@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit Positions: {{$position->name}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($position, ['url' => route("positions.update", $position), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	                @include('admin.positions._partials.form', ['buttonText'=>'Update'])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection