@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Create new expense category</div>
                
                <div class="card-body">
	                {!! Form::open([
	                'url' => route("expense_categories.store"),
	                'method'=>"POST",
	                'class'=>"needs-validation", "novalidate",
	                ]) !!}
	                @include('admin.expenseCategories._partials.form',['buttonText'=>"Create"])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection