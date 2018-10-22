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
        </style>
	
	    @stack('styles')

    </head>
    <body>
        <div class="container" id="registration">
            
            @include("_partials.alert")
	
	        {{Form::open(['url' => url('delegates'),'method'=>"POST", 'id'=>"payment-form"])}}
	
	        <input type="hidden" id="token" name="token" />
	        @include("_components.registration_form_basic_section")
	        @include("_components.registration_form_institution_section")
	        <tickets
			        :tickets="{{json_encode(\App\Ticket::whereEventId(1)->public()->available()->get())}}"
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
	        @include("_components.paymentForm")
	
	        <button class="btn btn-primary">Submit</button>
	
	        {{Form::close()}}
        </div>
        
        
        <script src="{{asset("js/manifest.js")}}"></script>
        <script src="{{asset("js/vendor.js")}}"></script>
        <script src="{{asset("js/frontEnd.js")}}"></script>
        
        @stack("scripts")
    </body>
</html>
