@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
         @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Ldap Users</div>
                
                <div class="table-responsive">
	                
	                <table class="table">
		                <thead>
		                    <th>Employee Id</th>
		                    <th>Principle Name</th>
		                    <th>Display Name</th>
		                    <th>Email</th>
		                    <th>HomeMdb</th>
		                    <th>Info</th>
		                    <th>Actions</th>
		                </thead>
		                <tbody>
		                    @foreach($users as $user)
			                    <tr>
				                    <td>{{$user->getEmployeeId()}}</td>
				                    <td>{{$user->getUserPrincipalName()}}</td>
				                    <td>{{$user->getDisplayName()}}</td>
				                    <td>{{$user->getEmail()}}</td>
				                    <td>{{$user->getHomeMdb()}}</td>
				                    <td>{{$user->getInfo()}}</td>
				                    <td>
					                    <button class="btn btn-success">Add</button>
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
