@extends("layouts.app")

@section("content")
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-10">
                @if (session('status'))
			        <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
		        @endif
		        <div class="card">
	                <div class="card-header">
		                <h2 class="d-inline-block m-0">Contacts for {{$vendor->name}}</h2>
		                <a class="mt-1 pull-right btn btn-sm btn-success"
		                   href="{{{route('vendors.vendorContacts.create',$vendor)}}}">
			                <i class="fa fa-plus-circle"
			                   aria-hidden="true"></i>
						</a>
	                </div>
		            
	                <div class="card-body">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                {{Form::open(['url'=>route('vendors.vendorContacts.index', $vendor),'method'=>'get'])}}
	                            <div class="input-group mb-3">
                                    <input type="text"
                                           name="keyword"
                                           class="form-control"
                                           placeholder="Search"
                                           aria-label="Search"
                                           aria-describedby="searchInput">
                                  <div class="input-group-append">
                                    <button class="btn btn-outline-secondary"
                                            type="submit">Find</button>
                                      <a href="{{route("vendors.vendorContacts.index", $vendor)}}"
                                         class="btn btn-outline-secondary">Clear</a>
                                  </div>
                                </div>
	                            {{Form::close()}}
                            </div>
                        </div>
                        <table class="table hover">
                           <thead>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Tel</th>
                               <th>Actions</th>
                           </thead>
                            <tbody>
                                @foreach($vendorContacts as $vendorContact)
	                                <tr>
		                                <td>{{$vendorContact->name}}</td>
		                                <td>{{$vendorContact->email}}</td>
		                                <td>{{$vendorContact->tel}}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a class="btn btn-info btn-sm text-light"
                                                   href="{{route('vendors.vendorContacts.edit',[$vendor,$vendorContact])}}">Edit</a>
                                            </div>
	
	                                        {{Form::open(['url'=>route('vendors.vendorContacts.destroy',[$vendor,$vendorContact]),'method'=>"DELETE",'style'=>'display:inline','@submit.prevent'=>"confirmDelete"])}}
	                                        <button class="btn btn-danger btn-sm"
	                                                type="submit">Delete</button>
	                                        {{Form::close()}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
		                {{ $vendorContacts->links() }}
                    </div>
	            </div>
	        </div>
	    </div>
	</div>
@endsection