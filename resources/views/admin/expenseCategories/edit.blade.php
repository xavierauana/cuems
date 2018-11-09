@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
             @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Edit expense category: {{$expenseCategory->name}}</div>
                
                <div class="card-body">
	                
	                {!! Form::model($expenseCategory, ['url' => route("expense_categories.update", $expenseCategory), 'method'=>"PUT", 'class'=>"needs-validation", "novalidate"]) !!}
	                @include('admin.expenseCategories._partials.form', ['buttonText'=>'Update'])
	                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection