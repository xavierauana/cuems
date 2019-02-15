@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
	        @if(session()->has('errors'))
		        <div class="alert alert-warning alert-dismissible fade show"
		             role="alert">
				  <strong>Something Wrong!</strong> You should check in on some of those fields below.
			        <ul class="list-unstyled">
				        @foreach(session()->get('errors') as $message )
					        <li>{{$message->getMessage()}}</li>
				        @endforeach
			        </ul>
				  <button type="button" class="close" data-dismiss="alert"
				          aria-label="Close">
				    <span aria-hidden="true">&times;</span>
				  </button>
				</div>
	        @endif
	        @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Ldap Users</div>
                
                <div class="table-responsive">
	                
	                <table class="table">
		                <thead>
		                    <th>CN</th>
		                    <th>Principle Name</th>
		                    <th>Display Name</th>
		                    <th>Email</th>
		                    <th>Actions</th>
		                </thead>
		                <tbody>
		                    @foreach($users as $user)
			                    <tr>
				                    <td>{{$user->getFirstAttribute('cn')}}</td>
				                    <td>{{$user->getUserPrincipalName()}}</td>
				                    <td>{{$user->getDisplayName()}}</td>
				                    <td>{{$user->getEmail()}}</td>
				                    <td>
					                    @if(App\User::whereEmail($user->getFirstAttribute('cn'))->count() === 0)
						                    {!! Form::open(['url'=>route('users.ldap'), 'method'=>"POST"]) !!}
						                    <input name="name"
						                           value="{{$user->getFirstAttribute('cn')}}"
						                           type="hidden">
						                    <input name="email"
						                           value="{{$user->getUserPrincipalName()}}"
						                           type="hidden">
						                    <button class="btn btn-success">Add</button>
						                    {!! Form::close() !!}
					                    @endif
				                    </td>
			                    </tr>
		                    @endforeach
		                    
		                </tbody>
	                </table>
	
	
	                {{--<p-table :headers="['Name','Email','Actions']"--}}
	                {{--:keys="['name','email']"--}}
	                {{--:paginator="{{json_encode($users)}}"--}}
	                {{--:has-action="true"--}}
	                {{--search-url="{{route('users.search')}}"--}}
	                {{--all-url="{{route('users.index')}}"--}}
	                {{-->--}}
	                {{--<template name="action" slot-scope="{item, deleteItem}">--}}
	                {{--<a :href="item.urls.edit"--}}
	                {{--class="btn btn-info btn-sm text-light">Edit</a>--}}
	                {{--<button :href="item.urls.delete"--}}
	                {{--@click.prevent="deleteItem(item)"--}}
	                {{--class="btn btn-danger btn-sm text-light">Delete</button>--}}
	                {{--</template>--}}
	                {{--</p-table>--}}
	                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
