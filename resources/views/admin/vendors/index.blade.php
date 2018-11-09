@extends('layouts.app')

@section('content')
	<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
         @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Institutions
                <a href="{{route('institutions.create')}}"
                   class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
	                <p-table :headers="['Name','Actions']"
	                         :keys="['name']"
	                         :paginator="{{json_encode($institutions)}}"
	                         :has-action="true"
	                         search-url="{{route('institutions.search')}}"
	                         all-url="{{route('institutions.index')}}"
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
