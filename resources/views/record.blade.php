@extends('layouts.app')

@section('content')
	<div class="container">
        @include("_partials.alert")
		<div class="row justify-content-center">
			<table class="table col">
				<tbody>
				@foreach($records as $record)
					<tr>
						<td>{{$record->invoice_id}}</td>
						<td>{{$record->status}}</td>
						<td>{{decrypt($record->form_data)}}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
        </div>
    </div>
@endsection

