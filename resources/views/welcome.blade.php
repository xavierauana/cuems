<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EMS</title>
	
	    <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600"
              rel="stylesheet" type="text/css" />
        
        <link href="{{asset("css/app.css")}}" rel="stylesheet"
              type="text/css" />
        
        <style>
            .invalid-feedback {
	            display: block;
            }

            body {
	            font-family: Helvetica, "微軟正黑體", "Microsoft JhengHei", sans-serif;
	            color: #524632;
	            background-image: url("{{asset('imgs/bg.jpg')}}");
	            background-size: cover;
	            background-repeat: no-repeat;
	            background-position-x: center;
	            background-position-y: top;
            }
        </style>
	
	    @stack('styles')

    </head>
    <body>
        <div class="container" id="registration">
        
        @include("_partials.alert")
	
	        {{Form::open(['url' => "",'method'=>"POST", 'id'=>"payment-form", 'onsubmit'=>"pay(event)"])}}
	
	        <input type="hidden" id="DO" name="DO" />
        <input type="hidden" id="token" name="token" />
	        @include("_components.registration_form_basic_section")
	        @include("_components.registration_form_institution_section")
	        <tickets
			        :tickets="{{json_encode(\App\Ticket::whereEventId($event->id)->public()->get())}}"
			        @select="update">
        @if ($errors->has('ticket'))
			        <template slot="errorMessage">
        <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('ticket') }}</strong>
        </span>
        </template>
		        @endif
        </tickets>
	        @include("_components.registration_trainee_section")
	
	        <div>{!! setting($event, "important_note") !!}</div>
        
        <div>{!! setting($event, "privacy") !!}</div>
        
        <button class="btn btn-primary">Submit</button>
	
	        {{Form::close()}}
        </div>

        <script src="{{asset("js/manifest.js")}}"></script>
        <script src="{{asset("js/vendor.js")}}"></script>
        <script src="{{asset("js/frontEnd.js")}}"></script>
        <script>
        function pay(e) {
          e.preventDefault()
          axios.post('/token?event={{$event->id}}', new FormData(e.target))
               .then(function (response) {

                 var tokenInput = document.getElementById("DO")
                 tokenInput.value = response.data.token
                 e.target.action = response.data.url
                 e.target.submit()
               })
        }
        </script>

        {{--<div class="container">--}}
        {{--<iframe src="http://dev.mect.cuhk.edu.hk?event=1" width="100%" height="800px"--}}
        {{--style="border: none"></iframe>--}}
        {{--</div>--}}


        @stack("scripts")
    
    
    </body>
</html>
