@extends('layouts.app')

@push('styles')
	<link rel="stylesheet"
	      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
	<div class="container">
    <div class="row">
	    <div class="col-sm-2 sidebar">
			<ul class="nav flex-column">
				<li class="nav-item dropdown">
					<a class="nav-link"
					   href="{{route('events.sessions.index', $event)}}">Sessions</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.delegates.index', $event)}}">Delegates</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.tickets.index', $event)}}">Tickets</a>
				</li>
				<li class="nav-item">
					<a class="nav-link"
					   href="{{route('events.transactions.index', $event)}}">Transactions</a>
				</li>
			</ul>
	    </div>
	    <div class="col">
		    {{$slot}}
	    </div>
    </div>
</div>
@endsection

@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	
	<script>
		_.forEach(document.querySelectorAll("input.date"), el => flatpickr(el, {
          enableTime: true,
          dateFormat: "d M Y H:i",
        }))
	</script>
@endpush

