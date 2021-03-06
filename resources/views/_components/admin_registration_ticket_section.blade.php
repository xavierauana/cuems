<fieldset class="ticket">
        <legend>Tickets:</legend>
         <div class="form-group row">
             <div class="col-sm-12">
                  @foreach(\App\Ticket::whereEventId($event->id)->get() as $ticket)
		             @include("_components.tickets", compact('ticket'))
	             @endforeach
             </div>
         </div>
	
	@if ($errors->has('ticket'))
		<span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('ticket') }}</strong>
        </span>
	@endif
</fieldset>