<?php
$transaction = $transaction ?? $delegate->transactions->first();
?>
testing transaction
Thank You {{$transaction->payee->name}} for joining the event: {{$event->title}}.