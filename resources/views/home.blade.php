@extends('layouts.app')

@section('content')
	<div class="container">
        @include("_partials.alert")
		<div class="row justify-content-center">
            
            @foreach($activeEvents as $event)
				<div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Active Event</div>
                        <div class="card-body">
                            <h3>{{$event->title}}</h3>
                            <a class="btn btn-info text-light"
                               href="{{route('events.details', $event)}}">Details</a>
                        </div>
                    </div>
                </div>
			@endforeach
        </div>
    </div>
@endsection
