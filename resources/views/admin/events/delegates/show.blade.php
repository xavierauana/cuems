@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
		        <div class="card-header  @if($delegate->is_duplicated === \App\Enums\DelegateDuplicationStatus::DUPLICATED) bg-warning @endif">Delegate: {{$delegate->name}}
			        @if($delegate->is_duplicated === \App\Enums\DelegateDuplicationStatus::DUPLICATED)
				        <span class="mt-1 float-right badge badge-warning">Duplicated</span>
			        @endif
                </div>
		        
		        <div class="card-body">
			        <section class="mb-3">
				        <h4>Basic Info</h4>
				        <div class="row">
					        <div class="col-sm-2">
						         <strong>Prefix: </strong> {{$delegate->prefix}}
					        </div>
					        <div class="col-sm-5">
						        <strong>First Name: </strong> {{$delegate->first_name}}
					        </div><div class="col-sm-5">
						        <strong>First Name: </strong> {{$delegate->last_name}}
					        </div>
				        </div>
				        <div class="row">
					        <div class="col-sm-6">
						        <strong>Email: </strong> {{$delegate->email}}
					        </div><div class="col-sm-6">
						        <strong>Mobile: </strong> {{$delegate->mobile}}
					        </div>
				        </div>
				        <hr>
				        <div class="row">
					        <div class="col-sm-12"><strong>Institution: </strong> {{$delegate->institution}}</div>
					        <div class="col-sm-6"><strong>Department: </strong> {{$delegate->department}}</div>
					        <div class="col-sm-6"><strong>Position: </strong> {{$delegate->position}}</div>
				        </div>
				        <hr>
				        <div class="row">
					        <div class="col-md-6"><strong>Address 1: </strong> {{$delegate->address_1}}</div>
					        <div class="col-md-6"><strong>Address 2: </strong> {{$delegate->address_2}}</div>
					        <div class="col-md-6"><strong>Address 3: </strong> {{$delegate->address_3}}</div>
					        <div class="col-md-6"><strong>Country: </strong> {{$delegate->country}}</div>
				        </div>
				        <hr>
				        <div class="row">
					        <div class="col-md-4"><strong>Ticket</strong> {{$delegate->transactions()->first()->ticket->name}}</div>
					        <div class="col-md-4"><strong>Transaction ID: </strong> {{$delegate->transactions()->first()->charge_id ?? "NA"}}</div>
					        <div class="col-md-4"><strong>Transaction Status: </strong> {{array_flip(\App\Enums\TransactionStatus::getStatus())[$delegate->transactions()->first()->status]  }}</div>
					        <div class="col-md-4"><strong>Role: </strong> {{$delegate->roles->first()->label}}</div>
				        </div>
				        <div class="row">
					        <div class="col"><strong>Note</strong> {{$delegate->transactions()->first()->note}}</div>
				        </div>
			        </section>
			        
			        <section>
		                <h4>Potential Duplicates</h4>
		                
		                <div class="table-responsive">
		                  <table class="table">
		                    <thead>
		                        <th>Name</th>
		                        <th>Email</th>
		                        <th>Mobile</th>
		                        <th>Institution</th>
		                        <th>Is Duplicated</th>
		                        <th>Role</th>
		                    </thead>
			                  <tbody>
			                  @foreach($duplicates as $duplicate)
				                  <tr>
					                  <td><a target="_blank"
					                         href="{{route('events.delegates.show',[$event, $duplicate])}}">{{$duplicate->name}}</a></td>
					                  <td>{{$duplicate->email}}</td>
					                  <td>{{$duplicate->mobile}}</td>
					                  <td>{{$duplicate->institution}}</td>
					                  <td>{{$duplicate->is_duplicated=== \App\Enums\DelegateDuplicationStatus::DUPLICATED?"DUPLICATED":"NO"}}</td>
					                  <td>{{$duplicate->roles->implode('label',', ')}}</td>
				                  </tr>
			                  @endforeach
			                  </tbody>
		                  </table>
		                </div>
		                
		                <a class="btn btn-primary"
		                   href="{{route('events.delegates.edit', [$event, $delegate])}}">Edit</a>
		                <a class="btn btn-info text-light"
		                   href="{{route('events.delegates.index', $event)}}">Back</a>
	                </section>
		        </div>
            </div>
        </div>
    </div>
@endcomponent