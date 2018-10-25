@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit setting: {{$setting->key}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($setting, ['url' => route("events.settings.update", [$event,$setting]), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	                @include('admin.events.settings._partials.form', ['buttonText'=>'Update'])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection