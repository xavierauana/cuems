<div class="form-group">
    <label style="margin-right: 15px">
        <input type="radio" name="ticket" value="{{$ticket->id}}"
               required /> {{$ticket->name}} {{"HK$".number_format($ticket->price,1)}}
    </label>
</div>