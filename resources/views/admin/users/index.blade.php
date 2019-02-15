@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
         @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Users
                <a href="{{route('users.create')}}"
                   class="btn btn-sm btn-success pull-right">New</a>
                <a href="{{route('users.ldap')}}"
                   class="btn btn-sm btn-success pull-right mr-3">Add From LDAP</a>
                </div>
                
                <div class="table-responsive">
	                <p-table :headers="['Name','Email','Actions']"
	                         :keys="['name','email']"
	                         :paginator="{{json_encode($users)}}"
	                         :has-action="true"
	                         search-url="{{route('users.search')}}"
	                         all-url="{{route('users.index')}}"
	                >
		                <template name="action" slot-scope="{item, deleteItem}">
			                <a :href="item.urls.edit"
			                   class="btn btn-info btn-sm text-light">Edit</a>
			                <button :href="item.urls.delete"
			                        @click.prevent="deleteItem(item)"
			                        class="btn btn-danger btn-sm text-light">Delete</button>
		                </template>
	                </p-table>
	                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
