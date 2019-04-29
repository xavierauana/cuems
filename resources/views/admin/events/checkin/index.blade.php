@component('admin._components.eventContainer', ['event'=>$event])
    <div class="row justify-content-center">
<div class="col">
@include("admin._partials.alert")
    <check-in :event-id="{{$event->id}}"></check-in>
</div>
</div>
@endcomponent