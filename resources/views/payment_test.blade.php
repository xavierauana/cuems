{{Form::open(['url'=>'/payment_test/status', 'method'=>'POST'])}}
<button> Check Status</button>
{{Form::close()}}

{{Form::open(['url'=>'/payment_test/token', 'method'=>'POST'])}}
<button> Get Token</button>
{{Form::close()}}