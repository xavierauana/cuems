<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
	
	
	<!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"
          type="text/css">
	
	<!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
          crossorigin="anonymous">
	
</head>
<body>
    <div id="app">
    
		{{Form::open(['url'=>'/payment_test/status', 'method'=>'POST', 'class'=>'mb-3'])}}
	    <button> Check Status</button>
	    {{Form::close()}}
	
	    {{Form::open(['url'=>'/payment_test/token', 'method'=>'POST', 'class'=>'mb-3'])}}
	    <button> Get Token</button>
	    {{Form::close()}}
	
	    <form id="paymentForm" method="POST" action="" onsubmit="pay(event)">
		    <input type="hidden" name="DO" id="token" />

			<button type="submit">Pay</button>
		</form>
    </div>


    <!-- Scripts -->
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>


<script>
	function pay(e) {
      e.preventDefault()
      axios.post('payment_test/token')
           .then(({data}) => {
             console.log(data)
             var tokenInput = document.getElementById("token")
             tokenInput.value = data.token
             e.target.action = data.url
           })
    }
</script>
</body>
</html>


