@extends('layouts.app')

@section('content')
	<div class="container">
        @include("_partials.alert")
		<div class="row justify-content-center">
			<table class="table">
				<tbody>
				@foreach($records as $record)
					<tr>
						<td>{{$record[]}}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
        </div>
    </div>
@endsection
