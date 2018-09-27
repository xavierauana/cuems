<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EMS</title>
	
	    <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600"
              rel="stylesheet" type="text/css" />
        
        <script src="https://js.stripe.com/v3/"></script>
        
        <link href="{{asset("css/app.css")}}" rel="stylesheet"
              type="text/css" />
        
        <style>
            .invalid-feedback {
	            display: block;
            }
        </style>

    </head>
    <body>
        <div class="container">
            @include("_partials.alert")
	        <form class="form needs-validation" novalidate id="payment-form"
	              action="{{url("delegates")}}"
	              method="POST">
                @csrf
		        <input type="hidden" name="token" id="token" />
                <fieldset class="basic">
                    <legend>Basic Information</legend>
                    
                    <div class="form-group row">
               
                        <label class="col-sm-2 col-form-label">Title:</label>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="prefix"
                                     value="prof
                                     required
        ">
                              <label class="form-check-label">Prof.</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="prefix"
                                     value="dr"
                                     required
                              >
                              <label class="form-check-label">Dr.</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="prefix"
                                     value="mr"
                                     required
                              >
                              <label class="form-check-label">Mr.</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="prefix"
                                     value="ms"
                                     required
                              >
                              <label class="form-check-label">Ms.</label>
                            </div>
	                        @if ($errors->has('prefix'))
		                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('prefix') }}</strong>
                                </span>
	                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Gender:</label>
                        <div class="col-sm-10">
                            
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="is_male"
                                     value="1"
                                     required
                              >
                              <label class="form-check-label">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="is_male"
                                     value="0"
                                     required
                              >
                              <label class="form-check-label">Female</label>
                            </div>
	                        @if ($errors->has('is_male'))
		                        <span class="invalid-feedback" role="alert">
                                    <strong>The gender field is required</strong>
                                </span>
	                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Surname</label>
                        <div class="col-sm-10">
                            <input class="form-control"
                                   name="first_name"
                                   required />
	                        @if ($errors->has('first_name'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
	                        @endif
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Given Name</label>
                        <div class="col-sm-10">
                            <input class="form-control"
                                   name="last_name"
                                   required />
	                        @if ($errors->has('last_name'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
	                        @endif
                        </div>
	                    
                    </div>
                    <div class="form-group row">
                        <label class="position col-sm-2 col-form-label">Position</label>
                        <div class="col-sm-10">
                            <input class="form-control"
                                   name="position"
                                   required />
	                        @if ($errors->has('position'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('position') }}</strong>
                                    </span>
	                        @endif
                        </div>
	                    
                    </div>
                    <div class="form-group row">
                        <label class="department col-sm-2 col-form-label">Department</label>
                        <div class="col-sm-10">
                        
                        <input class="form-control"
                               name="department"
                               required />
	                        @if ($errors->has('department'))
		                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('department') }}</strong>
                                </span>
	                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="institution col-sm-2 col-form-label">Institution</label>
                        <div class="col-sm-10">
                            <input class="form-control"
                                   name="institution"
                                   required />
	                        @if ($errors->has('institution'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('institution') }}</strong>
                                    </span>
	                        @endif
                        </div>
	                    
                    </div>
                    <div class="form-group row">
                        <label class="address col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                        
                            <input class="form-control"
                                   name="address"
                                   required />
	                        @if ($errors->has('address'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
	                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="email col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                        
                            <input class="form-control"
                                   name="email"
                                   required />
	                        @if ($errors->has('email'))
		                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
	                        @endif
                        </div>
	                    
                    </div>
                    <div class="form-group row">
                        <label class="mobile col-sm-2 col-form-label">Mobile Tel</label>
                        <div class="col-sm-10">
                        
                            <input class="form-control"
                                   name="mobile"
                                   required />
	                        @if ($errors->has('mobile'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
	                        @endif
                        </div>
	                    
                    </div>
                    <div class="form-group row">
                        <label class="fax col-sm-2 col-form-label">Fax</label>
                        <div class="col-sm-10">
                            <input class="form-control"
                                   name="fax" />
	                        @if ($errors->has('fax'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('fax') }}</strong>
                                    </span>
	                        @endif
                        </div>
	                    
                    </div>
                  
                 </fieldset>
                
                <fieldset class="ticket">
                    <legend>Tickets:</legend>
	                @foreach(\App\Ticket::whereEventId(1)->available()->get() as $ticket)
		                @include("_components.tickets", compact('ticket'))
	                @endforeach
	                @if ($errors->has('ticket'))
		                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('ticket') }}</strong>
                        </span>
	                @endif
                </fieldset>
                
                <fieldset class="trainee">
                    <legend>For Trainee</legend>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Training/Para-medical Organisation:</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="training_organisation" />
	                        @if ($errors->has('training_organisation'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('training_organisation') }}</strong>
                                    </span>
	                        @endif
                        </div>
	                    
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Address</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="training_organisation_address" />
	                        @if ($errors->has('training_organisation_address'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('training_organisation_address') }}</strong>
                                    </span>
	                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Name of supervisor:</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="supervisor" />
	                        @if ($errors->has('supervisor'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('supervisor') }}</strong>
                                    </span>
	                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Position:</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="supervisor_position" />
	                        @if ($errors->has('supervisor_postion'))
		                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('supervisor_postion') }}</strong>
                                    </span>
	                        @endif
                        </div>
                    </div>
                </fieldset>
                 <fieldset>
                    <legend>Payment</legend>
                    
                    <div class="form-group row">
                        <label class="fax col-sm-2 col-form-label">Credit or debit card</label>
                        <div class="col-sm-10">
                       <div id="card-element">
      <!-- A Stripe Element will be inserted here. -->
    </div>
                            <div id="card-errors" role="alert"></div>
                        </div>
                    </div>
                </fieldset>
                
                 <button class="btn btn-primary">Submit</button>
                
                
            </form>
        </div>
    
    <script>
        // Create a Stripe client.
        var stripe = Stripe('pk_test_3gflQJsHLK3hUItQx14JLbnk');

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
          base   : {
            color          : '#32325d',
            lineHeight     : '18px',
            fontFamily     : '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing  : 'antialiased',
            fontSize       : '16px',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
          invalid: {
            color    : '#fa755a',
            iconColor: '#fa755a'
          }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
          var displayError = document.getElementById('card-errors');
          if (event.error) {
            displayError.textContent = event.error.message;
          } else {
            displayError.textContent = '';
          }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
          event.preventDefault();

          stripe.createToken(card).then(function (result) {
            if (result.error) {
              // Inform the user if there was an error.
              var errorElement = document.getElementById('card-errors');
              errorElement.textContent = result.error.message;
            } else {
              // Send the token to your server.
              console.log(result)
              stripeTokenHandler(result.token);
            }
          });
        });

        function stripeTokenHandler(token) {
          var el   = document.getElementById("token"),

              form = document.getElementById("payment-form")

          console.log(token)
          el.value = token.id;
          form.submit()
        }
    </script>
    </body>
</html>
