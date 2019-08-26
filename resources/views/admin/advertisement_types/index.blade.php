@extends('layouts.app')

@section('content')
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
	             @include("admin._partials.alert")
		        <div class="card">
	                <div class="card-header">Advertisement Type
		                <a class="btn btn-sm pull-right btn-success text-light"
		                   href="{{route('advertisement_types.create')}}">New</a>
	                </div>
	                
	                <div class="card-body">
		                
		                <table class="table">
			                <thead>
			                <th>Name</th>
			                <th>Actions</th>
			                </thead>
			                <tbody>
			                @foreach($advertisementTypes as $type)
				                <tr>
					                <td>
						               {{$type->name}}
					                </td>
					                <td>
						                <a class="btn btn-info btn-sm text-light"
						                   href="{{route('advertisement_types.edit', $type)}}">Edit</a>
						                <button class="btn btn-danger btn-sm"
						                        type="button"
						                        href="{{route('advertisement_types.destroy', $type)}}">Destroy</button>
					                </td>
				                </tr>
			                @endforeach
			                </tbody>
		                </table>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
@endsection