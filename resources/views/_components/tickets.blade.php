<div class="form-group">
     <div class="form-check form-check-inline">
        <label style="margin-right: 15px">
            {{Form::radio('ticket_id', $ticket->id, null, ['class'=>'form-check-input','required'])}}
	        {{$ticket->name}} {{"HK$".number_format($ticket->price,1)}}
        </label>
     </div>
</div>