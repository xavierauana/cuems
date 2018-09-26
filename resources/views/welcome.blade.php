<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
	
	    <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600"
              rel="stylesheet" type="text/css" />
        
        <script src="https://js.stripe.com/v3/"></script>
        
        <link href="{{asset("css/app.css")}}" rel="stylesheet"
              type="text/css" />

    </head>
    <body>
        <div class="container">
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
                             name="title"
                             value="prof">
                      <label class="form-check-label">Prof.</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input"
                             type="radio"
                             name="title"
                             value="dr">
                      <label class="form-check-label">Dr.</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input"
                             type="radio"
                             name="title"
                             value="mr">
                      <label class="form-check-label">Mr.</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input"
                             type="radio"
                             name="title"
                             value="ms">
                      <label class="form-check-label">Ms.</label>
                    </div>
                    
                </div>
            </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Gender:</label>
                        <div class="col-sm-10">
                            
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="gender"
                                     value="male">
                              <label class="form-check-label">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input"
                                     type="radio"
                                     name="gender"
                                     value="female">
                              <label class="form-check-label">Female</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Surname</label>
                    <div class="col-sm-10">
                        <input class="form-control"
                               name="first_name"
                               required />
                    </div>
                </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Given Name</label>
                        <div class="col-sm-10">
                            <input class="form-control"
                                   name="last_name"
                                   required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="position col-sm-2 col-form-label">Position</label>
                        <div class="col-sm-10">
                        <input class="form-control"
                               name="position"
                               required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="department col-sm-2 col-form-label">Department</label>
                        <div class="col-sm-10">
                        
                        <input class="form-control"
                               name="department"
                               required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="institution col-sm-2 col-form-label">Institution</label>
                        <div class="col-sm-10">
                        
                        <input class="form-control"
                               name="institution"
                               required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="address col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                        
                        <input class="form-control"
                               name="address"
                               required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="email col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                        
                        <input class="form-control"
                               name="email"
                               required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="mobile col-sm-2 col-form-label">Mobile Tel</label>
                        <div class="col-sm-10">
                        
                        <input class="form-control"
                               name="mobile"
                               required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="fax col-sm-2 col-form-label">Fax</label>
                        <div class="col-sm-10">
                        <input class="form-control"
                               name="fax"
                               required />
                        </div>
                    </div>
                  
                 </fieldset>
                
                <fieldset class="ticket">
                    <legend>Tickets:</legend>
	                @foreach(\App\Ticket::whereEventId(1)->available()->get() as $ticket)
		                @include("_components.tickets", compact('ticket'))
	                @endforeach
                </fieldset>
                
                <fieldset class="trainee">
                    <legend>For Trainee</legend>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Training/Para-medical Organisation:</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="training_organisation" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Address</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="training_organisation_address" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Name of supervisor:</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="supervisor" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Position:</label>
                        <div class="col-sm-6 col-md-8  col-lg-9">
                            <input class="form-control"
                                   name="supervisor_position" />
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
